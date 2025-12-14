// Modal logic
const showFormBtn = document.getElementById('showFormBtn');
const formModal = document.getElementById('formModal');
const closeFormBtn = document.getElementById('closeFormBtn');

showFormBtn.addEventListener('click', () => {
	formModal.style.display = 'flex';
});
closeFormBtn.addEventListener('click', () => {
	formModal.style.display = 'none';
});

document.getElementById('interviewForm').addEventListener('submit', async function(e) {
	e.preventDefault();
	const hrd_name = document.getElementById('hrd_name').value;
	const name = document.getElementById('name').value;
	const division = document.getElementById('division').value;
	const communication = document.getElementById('communication').value;
	const attitude = document.getElementById('attitude').value;
	const problem_solving = document.getElementById('problem_solving').value;
	const teamwork = document.getElementById('teamwork').value;
	const notes = document.getElementById('notes').value;

	const res = await fetch('/submit', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ hrd_name, name, division, communication, attitude, problem_solving, teamwork, notes })
	});
	const data = await res.json();
	if (data.message === "Data Saved") {
		alert('Penilaian berhasil disimpan!');
		document.getElementById('interviewForm').reset();
		formModal.style.display = 'none';
		loadResults();
	} else {
		alert('Gagal menyimpan data: ' + (data.error || 'Unknown error'));
	}
});

// Statistik hasil interview dan card jumlah kandidat
async function loadResults() {
	const res = await fetch('/results');
	const data = await res.json();
	const statsDiv = document.getElementById('stats');
	const cardStats = document.getElementById('cardStats');
	statsDiv.innerHTML = '';
	cardStats.innerHTML = '';

	// Card jumlah kandidat per divisi
	data.forEach(divisi => {
		const total = divisi.candidates.length;
		const card = document.createElement('div');
		card.style.background = '#fff';
		card.style.borderRadius = '16px';
		card.style.boxShadow = '0 4px 16px rgba(0,0,0,0.08)';
		card.style.padding = '1.5rem 2rem';
		card.style.display = 'flex';
		card.style.flexDirection = 'column';
		card.style.alignItems = 'center';
		card.style.minWidth = '180px';
		card.style.marginBottom = '1rem';
		card.innerHTML = `<div style="font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">${divisi.division}</div>
			<div style="font-size:2.5rem;font-weight:700;color:#007bff;">${total}</div>
			<div style="font-size:0.95rem;color:#888;">Kandidat</div>`;
		cardStats.appendChild(card);
	});

	// Matriks: header aspek penilaian di atas, divisi di kanan, tampil 1 data top, tombol expand untuk 7 data
	const matrixContainer = document.createElement('div');
	matrixContainer.style.display = 'flex';
	matrixContainer.style.justifyContent = 'center';
	matrixContainer.style.alignItems = 'flex-start';
	matrixContainer.style.gap = '2rem';
	matrixContainer.style.margin = '2rem auto';

	// Aspek penilaian header
	const aspek = ['Nama', 'Komunikasi', 'Sikap', 'Problem Solving', 'Teamwork', 'Skor Akhir', 'Catatan', 'HRD Penilai'];
	const divisiList = data.map(d => d.division);

	// Matriks table
	const matrixTable = document.createElement('table');
	matrixTable.style.borderCollapse = 'collapse';
	matrixTable.style.boxShadow = '0 2px 12px rgba(0,0,0,0.07)';
	matrixTable.style.margin = '0 auto';
	matrixTable.style.textAlign = 'center';

	// Header aspek penilaian
	let theadHtml = '<thead><tr style="background:#f5f5f5;">';
	aspek.forEach(a => {
		theadHtml += `<th style="padding:0.75rem;">${a}</th>`;
	});
	theadHtml += '<th style="padding:0.75rem;">Aksi</th></tr></thead>';
	matrixTable.innerHTML = theadHtml + '<tbody></tbody>';
	const tbody = matrixTable.querySelector('tbody');

	// Tampilkan 1 data top tier tiap divisi, tombol expand untuk 7 data
	data.forEach(divisi => {
		const top = divisi.candidates[0];
		const tr = document.createElement('tr');
		tr.style.textAlign = 'center';
		if (top) {
			tr.innerHTML = `
				<td style="padding:0.75rem;">${top.name}</td>
				<td style="padding:0.75rem;">${top.communication}</td>
				<td style="padding:0.75rem;">${top.attitude}</td>
				<td style="padding:0.75rem;">${top.problem_solving}</td>
				<td style="padding:0.75rem;">${top.teamwork}</td>
				<td style="padding:0.75rem;font-weight:700;color:#007bff;">${top.final_score}</td>
				<td style="padding:0.75rem;">${top.notes || '-'}</td>
				<td style="padding:0.75rem;">${top.hrd_name || '-'}</td>
				<td style="padding:0.75rem;">
					<button class="expand-btn" style="padding:0.4rem 1rem;font-size:1rem;border-radius:6px;border:none;background:#007bff;color:#fff;cursor:pointer;" data-divisi="${divisi.division}">▼</button>
				</td>
			`;
		} else {
			tr.innerHTML = `<td colspan="9" style="text-align:center;padding:1.5rem;color:#aaa;">Belum ada data penilaian</td>`;
		}
		tbody.appendChild(tr);
	});
	matrixTable.appendChild(tbody);
	matrixContainer.appendChild(matrixTable);
	statsDiv.innerHTML = '';
	statsDiv.appendChild(matrixContainer);

	// Expand logic: tampilkan 7 data top tier tiap divisi
	document.querySelectorAll('.expand-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			const divisiName = this.getAttribute('data-divisi');
			const divisiData = data.find(d => d.division === divisiName);
			if (!divisiData) return;
			// Buat modal atau dropdown
			let modal = document.getElementById('expandModal');
			if (!modal) {
				modal = document.createElement('div');
				modal.id = 'expandModal';
				modal.style.position = 'fixed';
				modal.style.top = '0';
				modal.style.left = '0';
				modal.style.width = '100vw';
				modal.style.height = '100vh';
				modal.style.background = 'rgba(0,0,0,0.25)';
				modal.style.zIndex = '2000';
				modal.style.display = 'flex';
				modal.style.alignItems = 'center';
				modal.style.justifyContent = 'center';
				document.body.appendChild(modal);
			}
			modal.innerHTML = [
				`<div style="background:#fff;padding:2rem 2.5rem;border-radius:16px;max-width:700px;width:95vw;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.12);">`,
				`<button id="closeExpandBtn" style="position:absolute;top:10px;right:10px;font-size:1.5rem;background:none;border:none;cursor:pointer;">&times;</button>`,
				`<h2 style="font-size:1.3rem;font-weight:600;text-align:center;margin-bottom:1rem;">Top 7 ${divisiName}</h2>`,
				`<div style='overflow-x:auto;'>`,
				`<table style="width:100%;border-collapse:collapse;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.07);">`,
				`<thead><tr style="background:#f5f5f5;">${aspek.map(a => `<th style='padding:0.75rem;'>${a}</th>`).join('')}<th style='padding:0.75rem;'>Aksi</th></tr></thead>`,
				`<tbody>`,
				divisiData.candidates.slice(0,7).map(row => `
					<tr>
						<td style='padding:0.75rem;'>${row.name}</td>
						<td style='padding:0.75rem;'>${row.communication}</td>
						<td style='padding:0.75rem;'>${row.attitude}</td>
						<td style='padding:0.75rem;'>${row.problem_solving}</td>
						<td style='padding:0.75rem;'>${row.teamwork}</td>
						<td style='padding:0.75rem;font-weight:700;color:#007bff;'>${row.final_score}</td>
						<td style='padding:0.75rem;'>${row.notes || '-'}</td>
						<td style='padding:0.75rem;'>${row.hrd_name || '-'}</td>
						<td style='padding:0.75rem;'>
							<button class='delete-btn' data-id='${row.id}' style='background:#dc3545;color:#fff;border:none;padding:0.4rem 1rem;border-radius:6px;cursor:pointer;'>Hapus</button>
						</td>
					</tr>
				`).join(''),
				`</tbody></table></div></div>`
			].join('');
			modal.style.display = 'flex';
			document.getElementById('closeExpandBtn').onclick = () => {
				modal.style.display = 'none';
			};
			// Hapus data logic
			setTimeout(() => {
				document.querySelectorAll('.delete-btn').forEach(btn => {
					btn.onclick = async function() {
						if (confirm('Yakin ingin menghapus data ini?')) {
							const id = this.getAttribute('data-id');
							await fetch(`/delete/${id}`, { method: 'DELETE' });
							modal.style.display = 'none';
							loadResults();
						}
					};
				});
			}, 300);
		});
	});
}
loadResults();
