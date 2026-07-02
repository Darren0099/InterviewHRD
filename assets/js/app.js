
function tambahData() {

    Swal.fire({

        title: 'Tambah Data Kandidat',

        width: 700,

        html: `
        <form id="formTambah">

            <input
                class="form-control mb-2"
                name="nama_kandidat"
                placeholder="Nama Kandidat"
                required>

            <input
                class="form-control mb-2"
                name="nama_hrd"
                placeholder="Nama HRD"
                required>

            <select
                class="form-select mb-2"
                name="divisi"
                required>

                <option value="">Pilih Divisi</option>

                <option>Graphic Design</option>
                <option>Content Creator</option>
                <option>Finance</option>
                <option>Project Management</option>
                <option>Human Resource</option>
                <option>Public Relation</option>
                <option>Secretary</option>
                <option>Vice Leader</option>
                <option>Leader</option>
                <option>Social Media Management</option>

            </select>

            <input
                class="form-control mb-2"
                type="number"
                name="aspek_teknis"
                min="0"
                max="25"
                placeholder="Aspek Teknis">

            <input
                class="form-control mb-2"
                type="number"
                name="aspek_komunikasi"
                min="0"
                max="25"
                placeholder="Aspek Komunikasi">

            <input
                class="form-control mb-2"
                type="number"
                name="aspek_sikap"
                min="0"
                max="25"
                placeholder="Aspek Sikap">

            <input
                class="form-control mb-2"
                type="number"
                name="aspek_motivasi"
                min="0"
                max="25"
                placeholder="Aspek Motivasi">

            <textarea
                class="form-control"
                rows="3"
                name="catatan"
                placeholder="Catatan HRD"></textarea>

        </form>
        `,

        showCancelButton: true,

        confirmButtonText: 'Simpan',

        cancelButtonText: 'Batal',

        preConfirm: () => {

            let form = document.getElementById("formTambah");

            let fd = new FormData(form);

            let total =

                Number(fd.get("aspek_teknis")) +
                Number(fd.get("aspek_komunikasi")) +
                Number(fd.get("aspek_sikap")) +
                Number(fd.get("aspek_motivasi"));

            fd.append("total", total);

            fd.append("regional", REGIONAL_AKTIF);

            return fetch("simpan.php", {

                method: "POST",

                body: fd

            })
            .then(res => res.text())
            .then(res => {

                if(res !== "ok")
                    throw new Error(res);

            });

        }

    }).then((r)=>{

        if(r.isConfirmed){

            Swal.fire({

                icon:"success",

                title:"Berhasil",

                text:"Data berhasil ditambahkan."

            }).then(()=>{

                location.reload();

            });

        }

    });

}



// ---------------------
// Popup TOP Divisi
// ---------------------

function showTop(divisi){

    fetch(

        "top_divisi.php?divisi=" +

        encodeURIComponent(divisi) +

        "&regional=" +

        REGIONAL_AKTIF

    )

    .then(r=>r.text())

    .then(html=>{

        Swal.fire({

            title: divisi,

            html: html,

            width:900,

            confirmButtonText:"Tutup"

        });

    });

}



// ---------------------
// Hapus Regional
// ---------------------

function hapusRegional(){

    Swal.fire({

        title:"Hapus seluruh data regional?",

        text:"Seluruh data interview akan dihapus.",

        icon:"warning",

        showCancelButton:true,

        confirmButtonText:"Ya"

    }).then((r)=>{

        if(!r.isConfirmed)
            return;

        Swal.fire({

            title:"Konfirmasi Terakhir",

            text:"Data tidak dapat dikembalikan.",

            icon:"question",

            showCancelButton:true,

            confirmButtonText:"Hapus"

        }).then((x)=>{

            if(x.isConfirmed){

                location=

                "hapus_regional.php?regional="+

                REGIONAL_AKTIF;

            }

        });

    });

}



// ---------------------
// Hapus Kandidat
// ---------------------

function hapusData(id){

    Swal.fire({

        title:"Hapus kandidat?",

        text:"Data interview akan dihapus.",

        icon:"warning",

        showCancelButton:true,

        confirmButtonText:"Ya"

    }).then((r)=>{

        if(r.isConfirmed){

            location=

            "hapus.php?id="+id;

        }

    });

}



// ---------------------
// Edit Kandidat
// ---------------------

function editData(id){

    fetch(

        "get_data.php?id="+id

    )

    .then(r=>r.json())

    .then(d=>{

        Swal.fire({

            title:"Edit Kandidat",

            width:700,

            html:`

            <input id="nama"

            class="form-control mb-2"

            value="${d.nama_kandidat}">

            <input id="hrd"

            class="form-control mb-2"

            value="${d.nama_hrd}">

            <input id="t"

            type="number"

            class="form-control mb-2"

            value="${d.aspek_teknis}">

            <input id="k"

            type="number"

            class="form-control mb-2"

            value="${d.aspek_komunikasi}">

            <input id="s"

            type="number"

            class="form-control mb-2"

            value="${d.aspek_sikap}">

            <input id="m"

            type="number"

            class="form-control mb-2"

            value="${d.aspek_motivasi}">

            <textarea

            id="c"

            class="form-control">${d.catatan}</textarea>

            `,

            showCancelButton:true,

            confirmButtonText:"Update",

            preConfirm:()=>{

                let fd=new FormData();

                fd.append("id",id);

                fd.append("nama",nama.value);

                fd.append("hrd",hrd.value);

                fd.append("t",t.value);

                fd.append("k",k.value);

                fd.append("s",s.value);

                fd.append("m",m.value);

                fd.append("c",c.value);

                return fetch(

                    "update.php",

                    {

                        method:"POST",

                        body:fd

                    }

                )

                .then(r=>r.text())

                .then(r=>{

                    if(r!="ok")
                        throw r;

                });

            }

        })

        .then((r)=>{

            if(r.isConfirmed){

                Swal.fire({

                    icon:"success",

                    title:"Berhasil",

                    text:"Data berhasil diperbarui."

                }).then(()=>{

                    location.reload();

                });

            }

        });

    });

}