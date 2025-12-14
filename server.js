const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const Database = require('better-sqlite3');
const path = require('path');
const PDFDocument = require('pdfkit');
const db = new Database(path.join(__dirname, 'interview.db'));


const app = express();
app.use(cors());
app.use(bodyParser.json());
app.use(express.static('public'));

// Serve result page
app.get('/results-page', (req, res) => {
    res.sendFile(__dirname + '/views/result.html');
});

// Hapus data
app.delete('/delete/:id', (req, res) => {
    const id = req.params.id;

    const stmt = db.prepare('DELETE FROM interviews WHERE id = ?');
    stmt.run(id);

    res.json({ message: 'Data berhasil dihapus' });
});

// Submit data interview
app.post('/submit', (req, res) => {
    const { hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes } = req.body;

    const final_score = Number(communication) + Number(attitude) + Number(problem_solving) + Number(teamwork);

    // Check if candidate with same name and division exists
    const existing = db.prepare('SELECT id FROM interviews WHERE name = ? AND division = ?').get(name, division);

    let info;
    if (existing) {
        // Update existing record
        const updateStmt = db.prepare(`
            UPDATE interviews SET
            hrd_name = ?, communication = ?, attitude = ?, problem_solving = ?, teamwork = ?, notes = ?, final_score = ?
            WHERE id = ?
        `);
        info = updateStmt.run(hrd_name, communication, attitude, problem_solving, teamwork, notes, final_score, existing.id);
        res.json({ message: "Data Updated", id: existing.id, final_score });
    } else {
        // Insert new record
        const insertStmt = db.prepare(`
            INSERT INTO interviews
            (hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        `);
        info = insertStmt.run(hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score);
        res.json({ message: "Data Saved", id: info.lastInsertRowid, final_score });
    }
});

// Get results per division
app.get('/results', (req, res) => {
    const divisionLimits = {
        'Distrik Manager': 2,
        'Vice Distrik Manager': 2,
        'Public Relation': 7,
        'Project Management': 7,
        'Finance': 7,
        'Sekretaris': 7,
        'Human Resource': 7,
        'Graphic Design': 7,
        'Content Creator': 7,
        'Sosmed Management': 7
    };

    const divisions = Object.keys(divisionLimits);
    let results = [];

    divisions.forEach(div => {
        const stmt = db.prepare(`
            SELECT * FROM interviews 
            WHERE division = ? 
            ORDER BY final_score DESC 
            LIMIT ?
        `);

        const rows = stmt.all(div, divisionLimits[div]);
        results.push({ division: div, candidates: rows });
    });

    // urut sesuai list
    results.sort((a, b) => divisions.indexOf(a.division) - divisions.indexOf(b.division));

    res.json(results);
});

// Hapus semua data
app.delete('/delete-all', (req, res) => {
    try {
        const stmt = db.prepare('DELETE FROM interviews');
        const result = stmt.run();
        console.log('Delete all result:', result);
        res.json({ message: 'Semua data berhasil dihapus', changes: result.changes });
    } catch (error) {
        console.error('Error deleting all data:', error);
        res.status(500).json({ error: 'Gagal menghapus semua data', details: error.message });
    }
});

// Get all data for PDF
app.get('/all-data', (req, res) => {
    const divisions = [
        'Distrik Manager',
        'Vice Distrik Manager',
        'Public Relation',
        'Project Management',
        'Finance',
        'Sekretaris',
        'Human Resource',
        'Graphic Design',
        'Content Creator',
        'Sosmed Management'
    ];

    let results = [];
    divisions.forEach(div => {
        const stmt = db.prepare(`
            SELECT * FROM interviews
            WHERE division = ?
            ORDER BY final_score DESC
        `);
        const rows = stmt.all(div);
        results.push({ division: div, candidates: rows });
    });

    res.json(results);
});

// Download PDF
app.get('/download-pdf', (req, res) => {
    const divisions = [
        'Distrik Manager',
        'Vice Distrik Manager',
        'Public Relation',
        'Project Management',
        'Finance',
        'Sekretaris',
        'Human Resource',
        'Graphic Design',
        'Content Creator',
        'Sosmed Management'
    ];

    let results = [];
    divisions.forEach(div => {
        const stmt = db.prepare(`
            SELECT * FROM interviews
            WHERE division = ?
            ORDER BY final_score DESC
        `);
        const rows = stmt.all(div);
        results.push({ division: div, candidates: rows });
    });

    const doc = new PDFDocument();
    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', 'attachment; filename="penilaian_interview.pdf"');
    doc.pipe(res);

    doc.fontSize(20).text('Hasil Penilaian Interview', { align: 'center' });
    doc.moveDown();

    results.forEach(divisi => {
        doc.fontSize(16).text(divisi.division, { underline: true });
        doc.moveDown(0.5);

        divisi.candidates.forEach(candidate => {
            doc.fontSize(12).text(`Nama: ${candidate.name}`);
            doc.text(`HRD Penilai: ${candidate.hrd_name || '-'}`);
            doc.text(`Komunikasi: ${candidate.communication}`);
            doc.text(`Sikap: ${candidate.attitude}`);
            doc.text(`Problem Solving: ${candidate.problem_solving}`);
            doc.text(`Teamwork: ${candidate.teamwork}`);
            doc.text(`Skor Akhir: ${candidate.final_score}`);
            doc.text(`Catatan: ${candidate.notes || '-'}`);
            doc.moveDown();
        });
        doc.moveDown();
    });
    doc.end();
});

const PORT = process.env.PORT || 3001;

app.listen(PORT, '0.0.0.0', () => {
    console.log(`Server running on port ${PORT}`);
});

app.get('/', (req, res) => {
    res.status(200).send('Interview App is running');
});

app.use((req, res, next) => {
    console.log(`INCOMING REQUEST: ${req.method} ${req.url}`);
    next();
});
app.use((err, req, res, next) => {
    console.error('EXPRESS RUNTIME ERROR:', err);
    res.status(500).send('Internal Server Error');
});

