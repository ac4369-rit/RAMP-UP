<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * "Esta pregunta ya está respondida en la app": busca decisiones o preguntas con respuesta
 * que compartan al menos 2 palabras significativas con el texto de la nueva pregunta.
 */
final class QuestionMatcher
{
    private const STOP = [
        'that', 'this', 'with', 'what', 'when', 'where', 'which', 'from', 'have', 'does', 'your', 'about',
        'there', 'their', 'would', 'should', 'could', 'into', 'will', 'been', 'were', 'they', 'them', 'than', 'then', 'also',
    ];

    /** @return array{type: string, id: int, title: string}|null */
    public function find(PDO $pdo, int $projectId, string $text, ?int $ignoreQuestionId = null): ?array
    {
        $words = array_values(array_unique(array_filter(
            preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text)) ?: [],
            fn ($w) => mb_strlen($w) >= 4 && !in_array($w, self::STOP, true)
        )));
        $words = array_slice($words, 0, 8);
        if (count($words) < 2) {
            return null;
        }

        $best = null;
        $bestScore = 1;   // se exige al menos 2

        $st = $pdo->prepare('SELECT id, title, context FROM decisions WHERE project_id = :p');
        $st->execute(['p' => $projectId]);
        foreach ($st->fetchAll() as $d) {
            $score = $this->score($words, $d['title'] . ' ' . $d['context']);
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
            $score = $this->score($words, $q['body']);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = ['type' => 'question', 'id' => (int) $q['id'], 'title' => mb_substr($q['body'], 0, 80)];
            }
        }
        return $best;
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
