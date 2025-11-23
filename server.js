const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const db = require('./database');

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

    const stmt = db.prepare(`
        INSERT INTO interviews 
        (hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    `);

    const info = stmt.run(hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score);

    res.json({ message: "Data Saved", id: info.lastInsertRowid, final_score });
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

// Jalankan server
app.listen(3000, () => console.log('Server running on http://localhost:3000'));
