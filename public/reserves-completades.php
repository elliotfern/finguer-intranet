<h2>Estat 3: Reserves completades amb check-out del parking</h2>
<h4>Ordenat segons data sortida vehicle</h4>

<div class="container-fluid">
<div class='table-responsive' id='completades'>
<table class='table table-striped'>
<thead class="table-dark">
    <tr>
        <th>Núm. Comanda // ID</th>
        <th>Tipus</th>
        <th>Client // tel.</th>
        <th>Factura</th>
     </tr>
</thead>
<tbody></tbody>
</table>
</div>

<h5 id="numReservesCompletades"></h5>

</div>

<script>
function fetch_data(){
    let urlAjax = window.location.origin + "/api/reserves/get/?type=completades";
    $.ajax({
        url:urlAjax,
        method:"GET",
        dataType:"json",
        success:function(data){
            let html = '';
            for (let i=0; i<data.length; i++) {
                // Operaciones de manipulacion de las variables

                let tipo = data[i].tipo;
                let tipoReserva2 = "";
                if (tipo === 1) {
                    tipoReserva2 = "Finguer Class";
                } else if (tipo === 2) {
                    tipoReserva2 = "Gold Finguer Class";
                }

                // 0 - Inicio construccion body tabla
                html += '<tr>';

                // 1 - IdReserva
                html += '<td>';
                if (data[i].idReserva == 1) {
                    html += '<button type="button" class="btn btn-primary btn-sm">Client anual</button>';
                } else {
                    html += data[i].idReserva + ' // ' + data[i].id; 
                }
                html += '</td>';

                // 4 - Tipus de reserva
                html += '<td><a href="canvi-tipus-reserva.php?&id=' + data[i].id + '"><strong>' + tipoReserva2 + '</a></strong></td>';

                // 6 - Client i telefon
                html += '<td>';
                if (data[i].nombre) {
                    html += '<a href="canvi-client-telefon_nou.php?&id=' + data[i].id + '">' + data[i].nombre + ' // ' + data[i].tel + '</a>';
                } else {
                    html += '<a href="canvi-nom-client.php?&id=' + data[i].id + '">' + data[i].clientNom + ' ' + data[i].clientCognom + '</a> // <a href="canvi-client-telefon.php?&id=' + data[i].id + '">' + data[i].telefono + '</a>';
                }
                html += '</td>';
               
                // 15 - Enviar factura pdf
                html += '<td><a href="reserva-enviar-factura-pdf.php?&id=' + data[i].id + '" class="btn btn-primary btn-sm" role="button" aria-pressed="true">PDF</a></td>';

                html += '</tr>';
            }
            $('#completades tbody').html(html);
        }
    });
}

function fetch_data2(){
    let urlAjax = window.location.origin + "/api/reserves/get/?type=numReservesCompletades";
    $.ajax({
        url:urlAjax,
        method:"GET",
        dataType:"json",
        success:function(data){
            let html = '';
            for (let i=0; i<data.length; i++) {
                document.getElementById("numReservesCompletades").textContent = "Total reserves completades: " + data[i].numero;
            }
        }
    });
}

fetch_data();
fetch_data2();
</script>

<?php 
require_once(APP_ROOT . '/public/inc/footer.php');
?>