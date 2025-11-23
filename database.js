const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./interview.db');

db.serialize(() => {
  db.get("PRAGMA table_info(interviews)", (err, row) => {
    if (err || !row) {
      // Tabel belum ada, buat baru
      db.run(`CREATE TABLE IF NOT EXISTS interviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        hrd_name TEXT,
        name TEXT,
        division TEXT,
        communication INTEGER,
        attitude INTEGER,
        problem_solving INTEGER,
        teamwork INTEGER,
        notes TEXT,
        final_score INTEGER
      )`);
    } else {
      db.all("PRAGMA table_info(interviews)", (err, columns) => {
        const hasDivision = columns.some(col => col.name === 'division');
        const hasHRD = columns.some(col => col.name === 'hrd_name');
        if (!hasDivision || !hasHRD) {
          // Drop and recreate table
          db.run("DROP TABLE IF EXISTS interviews", () => {
            db.run(`CREATE TABLE interviews (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              hrd_name TEXT,
              name TEXT,
              division TEXT,
              communication INTEGER,
              attitude INTEGER,
              problem_solving INTEGER,
              teamwork INTEGER,
              notes TEXT,
              final_score INTEGER
            )`);
          });
        }
      });
    }
  });
});

module.exports = db;

