# Projet: LiveTransfer

---

# Models

---

## User

| **COLUMNS**  | id      | username | mail   | password | remember_token | role    | created_at | updated_at |
|--------------|:--------|----------|--------|----------|----------------|---------|------------|------------|
| **TYPE**     | bigint  | string   | string | string   | string         | boolean | timestamp  | timestamp  |
| **SPECIAL**  | primary |          | unique | hash     |                |         |            |            |
| **DEFAULT**  | next    |          |        |          |                | 0       | now()      | now()      |

## File

| **COLUMNS** | id      | name_origine | name_random | owner_id   | download_count | created_at | updated_at |
|-------------|:--------|--------------|-------------|------------|----------------|------------|------------|
| **TYPE**    | bigint  | string       | string      | bigint     | boolean        | timestamp  | timestamp  |
| **SPECIAL** | primary |              | unique      | user table |                |            |            |
| **DEFAULT** | next    |              |             |            | 0              | now()      | now()      |

# Relations

---

## File_User

| **COLUMNS** | user_id    | file_id    |
|-------------|:-----------|------------|
| **TYPE**    | bigint     | bigint     |
| **SPECIAL** | user table | file table |
