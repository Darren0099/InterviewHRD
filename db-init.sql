
CREATE TABLE IF NOT EXISTS candidates (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  position TEXT,
  interview_date TEXT,
  interviewer TEXT,
  communication INTEGER DEFAULT 0,
  attitude INTEGER DEFAULT 0,
  problem_solving INTEGER DEFAULT 0,
  mastery INTEGER DEFAULT 0,
  experience INTEGER DEFAULT 0,
  on_time INTEGER DEFAULT 0,
  docs_complete INTEGER DEFAULT 0,
  notes TEXT,
  soft_score REAL DEFAULT 0,
  hard_score REAL DEFAULT 0,
  admin_score REAL DEFAULT 0,
  total_score REAL DEFAULT 0,
  status TEXT DEFAULT 'Pending'
);
