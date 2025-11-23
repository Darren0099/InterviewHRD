const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const db = require('./database');

const app = express();
app.use(cors());
app.use(bodyParser.json());
app.use(express.static('public'));
app.get('/results-page', (req, res) => {
    res.sendFile(__dirname + '/views/result.html');
});

// Endpoint hapus data kandidat
app.delete('/delete/:id', (req, res) => {
    const id = req.params.id;
    db.run('DELETE FROM interviews WHERE id = ?', [id], function(err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Data berhasil dihapus' });
    });
});

app.post('/submit', (req, res) => {
    const { hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes } = req.body;
    const final_score = parseInt(communication) + parseInt(attitude) + parseInt(problem_solving) + parseInt(teamwork);

    db.run(`INSERT INTO interviews (hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes, final_score],
        function(err) {
            if (err) return res.status(500).json({ error: err.message });
            res.json({ message: "Data Saved", id: this.lastID, final_score });
        }
    );
});

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
    let pending = divisions.length;
    divisions.forEach(div => {
        db.all(`SELECT * FROM interviews WHERE division = ? ORDER BY final_score DESC LIMIT ?`, [div, divisionLimits[div]], (err, rows) => {
            if (err) return res.status(500).send(err.message);
            results.push({ division: div, candidates: rows });
            pending--;
            if (pending === 0) {
                // Sort results by division order
                results.sort((a, b) => divisions.indexOf(a.division) - divisions.indexOf(b.division));
                res.json(results);
            }
        });
    });
});

app.listen(3000, () => console.log('Server running on http://localhost:3000'));
