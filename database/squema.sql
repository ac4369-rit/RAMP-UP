SET NAMES utf8mb4;
SET time_zone = '+00:00';

DROP DATABASE IF EXISTS rampup;
CREATE DATABASE rampup
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rampup;

CREATE TABLE users (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    job_title      VARCHAR(100) NULL,
    avatar_url     VARCHAR(255) NULL,
    locale         VARCHAR(10)  NOT NULL DEFAULT 'en',
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE roles (
    code  VARCHAR(30) PRIMARY KEY
) ENGINE=InnoDB;

CREATE TABLE projects (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name              VARCHAR(150) NOT NULL,
    description       TEXT         NULL,
    audience          TEXT         NULL,
    current_status    TEXT         NULL,
    repo              VARCHAR(150) NULL,
    quiz_url          VARCHAR(255) NULL,
    sprint_number     SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    sprint_name       VARCHAR(100) NULL,
    sprint_goal       VARCHAR(255) NULL,
    sprint_starts_on  DATE         NOT NULL,
    sprint_ends_on    DATE         NOT NULL,
    created_by        INT UNSIGNED NULL,
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE memberships (
    project_id     INT UNSIGNED NOT NULL,
    user_id        INT UNSIGNED NOT NULL,
    role           VARCHAR(30)  NOT NULL,
    is_buddy       BOOLEAN      NOT NULL DEFAULT FALSE,
    replaces_name  VARCHAR(100) NULL,
    joined_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (project_id, user_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (role)       REFERENCES roles(code)
) ENGINE=InnoDB;

CREATE TABLE invitations (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id   INT UNSIGNED NOT NULL,
    email        VARCHAR(150) NOT NULL,
    invite_code  VARCHAR(64)  NOT NULL UNIQUE,
    role         VARCHAR(30)  NOT NULL,
    invited_by   INT UNSIGNED NULL,
    status       ENUM('pending','accepted','declined','expired') NOT NULL DEFAULT 'pending',
    expires_at   DATETIME     NOT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id)    ON DELETE SET NULL,
    FOREIGN KEY (role)       REFERENCES roles(code)
) ENGINE=InnoDB;

CREATE TABLE user_skills (
    user_id  INT UNSIGNED NOT NULL,
    skill    VARCHAR(60)  NOT NULL,
    PRIMARY KEY (user_id, skill),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_availability (
    user_id  INT UNSIGNED     NOT NULL,
    weekday  TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, weekday),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (weekday BETWEEN 1 AND 7)
) ENGINE=InnoDB;

CREATE TABLE project_tools (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    name        VARCHAR(80)  NOT NULL,
    url         VARCHAR(255) NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tool_access (
    tool_id     INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    status      ENUM('pending','granted') NOT NULL DEFAULT 'pending',
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (tool_id, user_id),
    FOREIGN KEY (tool_id) REFERENCES project_tools(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)         ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE intro_calls (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id   INT UNSIGNED     NOT NULL,
    newcomer_id  INT UNSIGNED     NOT NULL,
    buddy_id     INT UNSIGNED     NOT NULL,
    weekday      TINYINT UNSIGNED NOT NULL,
    minutes      TINYINT UNSIGNED NOT NULL DEFAULT 15,
    status       ENUM('requested','confirmed','cancelled') NOT NULL DEFAULT 'requested',
    created_at   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)  REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (newcomer_id) REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (buddy_id)    REFERENCES users(id)    ON DELETE CASCADE,
    CHECK (weekday BETWEEN 1 AND 7)
) ENGINE=InnoDB;

CREATE TABLE team_posts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    body        TEXT         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE team_rules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED     NOT NULL,
    position    TINYINT UNSIGNED NOT NULL,
    rule_text   VARCHAR(255)     NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notification_settings (
    user_id               INT UNSIGNED NOT NULL,
    project_id            INT UNSIGNED NOT NULL,
    only_mentions         BOOLEAN      NOT NULL DEFAULT TRUE,
    daily_summary         BOOLEAN      NOT NULL DEFAULT TRUE,
    daily_summary_time    TIME         NOT NULL DEFAULT '18:00:00',
    all_channel_messages  BOOLEAN      NOT NULL DEFAULT FALSE,
    PRIMARY KEY (user_id, project_id),
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE milestones (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED     NOT NULL,
    position    TINYINT UNSIGNED NOT NULL,
    name        VARCHAR(150)     NOT NULL,
    status      ENUM('done','current','upcoming') NOT NULL DEFAULT 'upcoming',
    due_on      DATE             NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE change_events (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id   INT UNSIGNED NOT NULL,
    kind         VARCHAR(30)  NOT NULL,
    title        VARCHAR(150) NOT NULL,
    detail       TEXT         NULL,
    area         VARCHAR(30)  NULL,
    happened_at  DATETIME     NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (area)       REFERENCES roles(code)
) ENGINE=InnoDB;

CREATE TABLE decisions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id    INT UNSIGNED NOT NULL,
    title         VARCHAR(150) NOT NULL,
    area          VARCHAR(30)  NOT NULL,
    context       TEXT         NOT NULL,
    consequences  TEXT         NULL,
    status        ENUM('proposed','accepted','superseded') NOT NULL DEFAULT 'accepted',
    decided_at    DATETIME     NOT NULL,
    decided_by_id INT UNSIGNED NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (area)          REFERENCES roles(code),
    FOREIGN KEY (decided_by_id) REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE decision_options (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    decision_id  INT UNSIGNED NOT NULL,
    position     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    label        VARCHAR(150) NOT NULL,
    description  VARCHAR(255) NULL,
    chosen       BOOLEAN      NOT NULL DEFAULT FALSE,
    FOREIGN KEY (decision_id) REFERENCES decisions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE decision_roles (
    decision_id  INT UNSIGNED NOT NULL,
    role         VARCHAR(30)  NOT NULL,
    PRIMARY KEY (decision_id, role),
    FOREIGN KEY (decision_id) REFERENCES decisions(id) ON DELETE CASCADE,
    FOREIGN KEY (role)        REFERENCES roles(code)
) ENGINE=InnoDB;

CREATE TABLE decision_reads (
    decision_id  INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED NOT NULL,
    read_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (decision_id, user_id),
    FOREIGN KEY (decision_id) REFERENCES decisions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE glossary_terms (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    term        VARCHAR(100) NOT NULL,
    definition  TEXT         NOT NULL,
    used_in     VARCHAR(150) NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE resources (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id      INT UNSIGNED NOT NULL,
    role            VARCHAR(30)  NOT NULL,
    title           VARCHAR(150) NOT NULL,
    url             VARCHAR(255) NOT NULL,
    kind            ENUM('guide','repo','design','doc') NOT NULL DEFAULT 'doc',
    verified_at     DATETIME     NULL,
    verified_by_id  INT UNSIGNED NULL,
    FOREIGN KEY (project_id)     REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (role)           REFERENCES roles(code),
    FOREIGN KEY (verified_by_id) REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE questions (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id           INT UNSIGNED NOT NULL,
    author_id            INT UNSIGNED NOT NULL,
    to_user_id           INT UNSIGNED NULL,
    area                 VARCHAR(30)  NOT NULL,
    body                 TEXT         NOT NULL,
    related_decision_id  INT UNSIGNED NULL,
    created_at           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)          REFERENCES projects(id)  ON DELETE CASCADE,
    FOREIGN KEY (author_id)           REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (to_user_id)          REFERENCES users(id)     ON DELETE SET NULL,
    FOREIGN KEY (area)                REFERENCES roles(code),
    FOREIGN KEY (related_decision_id) REFERENCES decisions(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE answers (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id  INT UNSIGNED NOT NULL,
    author_id    INT UNSIGNED NOT NULL,
    body         TEXT         NOT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id)   REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_attempts (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED     NOT NULL,
    project_id     INT UNSIGNED     NOT NULL,
    score_percent  TINYINT UNSIGNED NOT NULL,
    taken_at       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CHECK (score_percent <= 100)
) ENGINE=InnoDB;

CREATE TABLE tasks (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id   INT UNSIGNED NOT NULL,
    title        VARCHAR(150) NOT NULL,
    description  TEXT         NULL,
    status       ENUM('todo','in_progress','blocked','review','done') NOT NULL DEFAULT 'todo',
    is_starter   BOOLEAN      NOT NULL DEFAULT FALSE,
    priority     ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    assigned_to  INT UNSIGNED NULL,
    due_date     DATE         NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)  REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE task_updates (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id     INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    kind        ENUM('progress','blocked','review','feedback','handoff','accepted') NOT NULL,
    body        TEXT         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE github_cache (
    cache_key   VARCHAR(190) PRIMARY KEY,
    payload     MEDIUMTEXT   NOT NULL,
    fetched_at  DATETIME     NOT NULL
) ENGINE=InnoDB;
