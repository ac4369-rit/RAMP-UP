<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * "Esta pregunta ya está respondida en la app": busca decisiones o preguntas con respuesta
 * que compartan palabras significativas con el texto de la nueva pregunta.
 *
 * Works in every language:
 * - the stored English text AND its translation (tc()) are both compared, so a question
 *   typed in Croatian or Japanese can match content that is stored in English;
 * - Japanese has no spaces between words, so for Japanese/Chinese text we compare
 *   pairs of characters (e.g. 請求書 → 請求, 求書) instead of words.
 */
final class QuestionMatcher
{
    private const STOP = [
        // English
        'that', 'this', 'with', 'what', 'when', 'where', 'which', 'from', 'have', 'does', 'your', 'about',
        'there', 'their', 'would', 'should', 'could', 'into', 'will', 'been', 'were', 'they', 'them', 'than', 'then', 'also',
        // Croatian
        'koji', 'koja', 'koje', 'kojeg', 'gdje', 'kada', 'zašto', 'kako', 'može', 'mogu', 'treba', 'trebaju', 'trebam',
        'nije', 'jesu', 'biti', 'samo', 'imamo', 'ovaj', 'ova', 'ovo', 'svaki', 'neki', 'prije', 'poslije', 'nešto',
    ];

    /** Characters that are written without spaces (Han, Hiragana, Katakana). */
    private const CJK = '/[\p{Han}\p{Katakana}\p{Hiragana}ー]/u';

    /** @return array{type: string, id: int, title: string}|null */
    public function find(PDO $pdo, int $projectId, string $text, ?int $ignoreQuestionId = null): ?array
    {
        $words = $this->keywords($text);
        // Character pairs are shorter than words, so Japanese needs one more shared piece to count as a match.
        $needed = preg_match(self::CJK, $text) ? 3 : 2;
        if (count($words) < $needed) {
            return null;
        }

        $best = null;
        $bestScore = $needed - 1;

        $st = $pdo->prepare('SELECT id, title, context FROM decisions WHERE project_id = :p');
        $st->execute(['p' => $projectId]);
        foreach ($st->fetchAll() as $d) {
            $haystack = $d['title'] . ' ' . $d['context'] . ' ' . tc($d['title']) . ' ' . tc($d['context']);
            $score = $this->score($words, $haystack);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = ['type' => 'decision', 'id' => (int) $d['id'], 'title' => $d['title']];
            }
        }

        $st = $pdo->prepare(
            'SELECT q.id, q.body FROM questions q
              WHERE q.project_id = :p AND q.id <> :ignore
                AND EXISTS (SELECT 1 FROM answers a WHERE a.question_id = q.id)'
        );
        $st->execute(['p' => $projectId, 'ignore' => $ignoreQuestionId ?? 0]);
        foreach ($st->fetchAll() as $q) {
            $score = $this->score($words, $q['body'] . ' ' . tc($q['body']));
            if ($score > $bestScore) {
                $bestScore = $score;
                // Full text: the page translates it with tc() and then shortens it.
                $best = ['type' => 'question', 'id' => (int) $q['id'], 'title' => $q['body']];
            }
        }
        return $best;
    }

    /** @return list<string> the important pieces of the text (max. 12) */
    private function keywords(string $text): array
    {
        $text = mb_strtolower($text);
        $pieces = [];

        // Words in languages with spaces (English, Croatian…)
        foreach (preg_split('/[^\p{L}\p{N}]+/u', $text) ?: [] as $w) {
            if (mb_strlen($w) >= 4 && !in_array($w, self::STOP, true) && !preg_match(self::CJK, $w)) {
                $pieces[] = $w;
            }
        }

        // Japanese/Chinese: pairs of characters from runs of Kanji/Katakana (the meaning-carrying parts;
        // Hiragana is mostly grammar like は, の, を and is left out).
        preg_match_all('/[\p{Han}\p{Katakana}ー]{2,}/u', $text, $runs);
        foreach ($runs[0] as $run) {
            $chars = mb_str_split($run);
            for ($i = 0; $i < count($chars) - 1; $i++) {
                $pieces[] = $chars[$i] . $chars[$i + 1];
            }
        }

        return array_slice(array_values(array_unique($pieces)), 0, 12);
    }

    private function score(array $words, string $haystack): int
    {
        $h = mb_strtolower($haystack);
        $n = 0;
        foreach ($words as $w) {
            if (mb_strpos($h, $w) !== false) {
                $n++;
            }
        }
        return $n;
    }
}
