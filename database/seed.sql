USE rampup;

SET time_zone = '+00:00';

INSERT INTO roles (code) VALUES ('backend'), ('design'), ('frontend'), ('product');

INSERT INTO users (name, email, password_hash, job_title, locale) VALUES
('Maya Chen',  'maya@rampup.test',  '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Project Lead',       'en'),
('Leo Park',   'leo@rampup.test',   '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Senior Developer',   'en'),
('Sam Ellis',  'sam@rampup.test',   '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Frontend Developer', 'en'),
('Alex Novak', 'alex@rampup.test',  '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'UX Designer',        'hr'),
('Ana Kovač',  'ana@rampup.test',   '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Project Lead',       'hr'),
('Đuro Šimić', 'duro@rampup.test',  '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Frontend Developer', 'hr'),
('ليلى حداد',   'layla@rampup.test', '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Backend Developer',  'en'),
('佐藤 結衣',    'yui@rampup.test',   '$2y$10$siU7HbC7r.f2/ssxRGi7SuZY..wTn7DmiXtQ5g/EOOnQgkhXsbFyy', 'Junior Developer',   'ja');

INSERT INTO projects (name, description, audience, current_status, github_repo, quiz_url, sprint_number, sprint_name, sprint_goal, sprint_starts_on, sprint_ends_on, created_by) VALUES
('Atlas – Customer Portal',
 'A web portal where customers can see their orders, download invoices and contact support in one place.',
 'Existing customers of a mid-sized online shop and the support team that answers their questions.',
 'Login and order history are finished. The invoice page is in progress and support chat starts next sprint.',
 'ac4369-rit/RAMP-UP', 'https://forms.example.com/atlas-quiz',
 4, 'Invoices', 'Customers can download every invoice as a PDF.',
 '2026-11-02', '2026-11-20', 1),
('Library Booking System',
 'Online system for booking study rooms at the university library.',
 'Students who need a quiet room and the library staff who manage the rooms.',
 'Planning phase: wireframes are being reviewed.',
 NULL, NULL,
 1, 'Discovery', 'Agree on the booking flow with the library staff.',
 '2026-11-01', '2026-11-28', 5);

INSERT INTO memberships (project_id, user_id, role, is_buddy, replaces_name, joined_at) VALUES
(1, 1, 'product',  FALSE, NULL,     '2026-09-01 08:00:00'),
(1, 2, 'backend',  TRUE,  NULL,     '2026-09-01 08:30:00'),
(1, 3, 'frontend', FALSE, NULL,     '2026-09-15 09:00:00'),
(1, 4, 'design',   FALSE, 'Jordan', '2026-11-10 08:00:00'),
(2, 5, 'product',  FALSE, NULL,     '2026-11-01 08:00:00'),
(2, 6, 'frontend', TRUE,  NULL,     '2026-11-01 08:30:00'),
(2, 7, 'backend',  FALSE, NULL,     '2026-11-02 09:00:00'),
(2, 8, 'frontend', FALSE, NULL,     '2026-11-05 09:00:00');

INSERT INTO invitations (project_id, email, invite_code, role, invited_by, status, expires_at) VALUES
(1, 'alex@rampup.test', 'ATLAS-7F3K9Q',  'design',  1, 'accepted', '2026-11-17 22:59:59'),
(1, 'new@rampup.test',  'ATLAS-X2M8LP',  'backend', 1, 'pending',  '2026-12-31 22:59:59'),
(2, 'alex@rampup.test', 'LIBRARY-4H6TZ', 'design',  5, 'pending',  '2026-12-31 22:59:59');

INSERT INTO user_skills (user_id, skill) VALUES
(4, 'UX Design'), (4, 'Figma'), (4, 'Python'), (4, 'Visual Design'),
(2, 'PHP'), (2, 'MySQL'), (2, 'Code Review'),
(3, 'CSS'), (3, 'Accessibility');

INSERT INTO user_availability (user_id, weekday) VALUES
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5);

INSERT INTO project_tools (project_id, name, url) VALUES
(1, 'Figma',        'https://figma.com/file/example-atlas'),
(1, 'Google Drive', 'https://drive.google.com/example-atlas'),
(1, 'Discord',      'https://discord.gg/example-atlas'),
(1, 'GitHub',       'https://github.com/ac4369-rit/RAMP-UP');

INSERT INTO tool_access (tool_id, user_id, status) VALUES
(1, 4, 'granted'),
(2, 4, 'granted'),
(3, 4, 'pending'),
(4, 4, 'pending');

INSERT INTO team_rules (project_id, position, rule_text) VALUES
(1, 1, 'Meetings: Tuesday 18:00 on Discord'),
(1, 2, 'Reply within 24 hours'),
(1, 3, 'Decisions go in the #decisions channel'),
(1, 4, 'Make file names easy to find');

INSERT INTO notification_settings (user_id, project_id, only_mentions, daily_summary, daily_summary_time, all_channel_messages) VALUES
(4, 1, TRUE, TRUE, '18:00:00', FALSE);

INSERT INTO team_posts (project_id, user_id, body, created_at) VALUES
(1, 3, 'Hi everyone, I am Sam. I build the screens customers see, so ping me about anything visual!', '2026-09-15 09:30:00'),
(1, 1, 'Welcome to the team, Alex! Leo will be your buddy for the first weeks.',                     '2026-11-10 08:05:00');

INSERT INTO milestones (project_id, position, name, status, due_on) VALUES
(1, 1, 'Login and accounts',      'done',     '2026-10-01'),
(1, 2, 'Order history',           'done',     '2026-10-20'),
(1, 3, 'Invoice page',            'current',  '2026-11-20'),
(1, 4, 'Support chat',            'upcoming', '2026-12-11'),
(1, 5, 'Public launch',           'upcoming', '2027-01-31');

INSERT INTO change_events (project_id, kind, title, detail, happened_at) VALUES
(1, 'decision', 'Invoices will be PDF only',          'HTML invoices were dropped to save time.',            '2026-11-03 10:00:00'),
(1, 'people',   'Jordan left the team',               'Jordan moved to another project. Alex takes over UX.', '2026-11-03 15:00:00'),
(1, 'scope',    'Support chat moved to next sprint',  'The invoice page needed more time.',                  '2026-11-05 09:00:00'),
(1, 'release',  'Order history released to testers', 'First 20 customers can now see their orders.',        '2026-11-07 14:00:00');

INSERT INTO decisions (project_id, title, context, consequences, decided_at, decided_by) VALUES
(1, 'Use PHP and MySQL for the backend',
    'The whole team already knows PHP and MySQL, and the school server supports them.',
    'No new framework to learn. We use Composer for the few libraries we need.',
    '2026-09-05 10:00:00', 2),
(1, 'Invoices are generated as PDF only',
    'Building both HTML and PDF invoices would take two extra weeks.',
    'Customers download a PDF. The design team must create one PDF template.',
    '2026-11-03 10:00:00', 1),
(1, 'Mobile-first layout for every screen',
    'Most customers open the portal on their phone.',
    'All designs start at 360px width. Desktop is an enlarged version.',
    '2026-09-20 11:00:00', 1),
(1, 'Login with email and password, no social login',
    'Social login needs extra privacy checks the client does not want yet.',
    'Only email and password. Passwords are stored as hashes.',
    '2026-09-12 09:00:00', 2);

INSERT INTO decision_options (decision_id, label, description, chosen) VALUES
(1, 'PHP + MySQL',      'Everyone knows it, runs on the school server.', TRUE),
(1, 'Node.js + MongoDB', 'Modern, but only one person knows it.',         FALSE),
(2, 'PDF only',          'One template, faster to build.',                TRUE),
(2, 'HTML and PDF',      'Two templates, two extra weeks.',               FALSE);

INSERT INTO decision_roles (decision_id, role) VALUES
(1, 'backend'), (1, 'frontend'),
(2, 'design'), (2, 'backend'), (2, 'product'),
(3, 'design'), (3, 'frontend'),
(4, 'backend'), (4, 'design');

INSERT INTO decision_reads (decision_id, user_id, read_at) VALUES
(3, 4, '2026-11-10 09:00:00');

INSERT INTO glossary_terms (project_id, term, definition, used_in) VALUES
(1, 'Order',        'Everything a customer bought in one checkout.',               'Order history page'),
(1, 'Invoice',      'The official PDF bill for one order.',                         'Invoice page'),
(1, 'Ticket',       'A support request a customer sends to the support team.',      'Support chat'),
(1, 'Sprint',       'A two-to-three-week block of work with a fixed goal.',          'Planning, Where are we now'),
(1, 'Handoff',      'Passing finished design work to developers with all details.', 'Figma, task board');

INSERT INTO resources (project_id, role, title, url, kind, verified_at, verified_by_id) VALUES
(1, 'design',  'Design onboarding guide', 'https://example.com/atlas/design-guide', 'guide',  '2026-11-05 10:00:00', 1),
(1, 'design',  'Figma file: Atlas',       'https://figma.com/file/example-atlas',   'design', '2026-11-08 12:00:00', 1),
(1, 'design',  'Old style guide (v1)',    'https://example.com/atlas/style-v1.pdf', 'doc',    '2026-08-01 10:00:00', 1),
(1, 'design',  'Accessibility checklist', 'https://example.com/atlas/a11y.md',      'doc',    NULL,                  NULL),
(1, 'backend', 'Backend setup guide',     'https://example.com/atlas/backend',      'guide',  '2026-11-01 09:00:00', 2),
(1, 'backend', 'Code repository',         'https://github.com/ac4369-rit/RAMP-UP',  'repo',   '2026-11-01 09:00:00', 2),
(1, 'frontend','Component list',          'https://example.com/atlas/components',   'doc',    '2026-10-25 09:00:00', 3);

INSERT INTO questions (project_id, author_id, to_user_id, area, body, related_decision_id, created_at) VALUES
(1, 3, 1,    'product', 'Do customers need to download old invoices from before the portal existed?', 2,    '2026-11-04 10:00:00'),
(1, 4, 2,    'design',  'Which PDF template should the invoice design follow?',                      2,    '2026-11-10 10:00:00'),
(1, 4, NULL, 'design',  'Where can I find the latest logo files?',                                    NULL, '2026-11-10 11:00:00');

INSERT INTO answers (question_id, author_id, body, created_at) VALUES
(1, 1, 'Only invoices from the last 12 months. Older ones stay in the old system.', '2026-11-04 12:00:00'),
(2, 2, 'Use the template in the Figma file, page "Invoice PDF".',                    '2026-11-10 13:00:00');

INSERT INTO quiz_attempts (user_id, project_id, score_percent, taken_at) VALUES
(3, 1, 90, '2026-09-20 10:00:00');

INSERT INTO tasks (project_id, title, description, status, is_starter, assigned_to, due_date) VALUES
(1, 'Review the invoice page wireframe', 'Leave comments on the invoice page design in Figma.', 'todo',        TRUE,  4,    '2026-11-20'),
(1, 'Fix contrast on the login button',  'The button text fails the 4.5:1 contrast check.',     'todo',        TRUE,  NULL, '2026-11-25'),
(1, 'Build invoice download',            'Customers can download a PDF of each invoice.',       'in_progress', FALSE, 2,    '2026-11-30'),
(1, 'Test order history on mobile',      'Check the page at 320px width and 200% zoom.',        'review',      FALSE, 3,    '2026-11-18'),
(1, 'Set up database',                   'Create tables and demo data.',                        'done',        FALSE, 2,    '2026-09-15');

INSERT INTO task_updates (task_id, user_id, kind, body, created_at) VALUES
(3, 2, 'progress', 'PDF generation works, download button still missing.', '2026-11-09 16:00:00'),
(4, 3, 'review',   'Ready for review, tested on 3 phones.',                '2026-11-11 15:00:00');
