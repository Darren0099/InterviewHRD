const Database = require("better-sqlite3");
const db = new Database("./interview.db");

// Cek apakah tabel sudah ada
const tableInfo = db.prepare(`PRAGMA table_info(interviews)`).all();

if (tableInfo.length === 0) {
    // Tabel TIDAK ADA → buat baru
    createTable();
} else {
    // Cek apakah kolom wajib ada
    const columns = tableInfo.map(col => col.name);

    const requiredColumns = [
        "id",
        "hrd_name",
        "name",
        "division",
        "communication",
        "attitude",
        "problem_solving",
        "teamwork",
        "notes",
        "final_score"
    ];

    const missingColumn = requiredColumns.some(col => !columns.includes(col));

    if (missingColumn) {
        // Struktur salah → drop + buat ulang
        db.prepare("DROP TABLE IF EXISTS interviews").run();
        createTable();
    }
}

function createTable() {
    db.exec(`
        CREATE TABLE interviews (
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
        );
    `);
}

module.exports = db;
