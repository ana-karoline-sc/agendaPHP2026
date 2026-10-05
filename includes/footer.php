<footer class="main-footer">

    <strong>
        Copyright &copy; 2020 - Todos os direitos reservados.
    </strong>

    <div class="float-right d-none d-sm-inline-block">
        <b>AG Versão</b> 2.0
    </div>

</footer>


<!-- =====================================================
     CONTROL SIDEBAR
===================================================== -->

<aside class="control-sidebar control-sidebar-dark">
</aside>


</div>
<!-- ./wrapper -->


<!-- =====================================================
     JQUERY
===================================================== -->

<script src="../plugins/jquery/jquery.min.js"></script>


<!-- =====================================================
     JQUERY UI
===================================================== -->

<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>


<!-- =====================================================
     BOOTSTRAP
===================================================== -->

<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


<!-- =====================================================
     CHART JS
===================================================== -->

<script src="../plugins/chart.js/Chart.min.js"></script>


<!-- =====================================================
     SPARKLINE
===================================================== -->

<script src="../plugins/sparklines/sparkline.js"></script>


<!-- =====================================================
     JQVMAP
===================================================== -->

<script src="../plugins/jqvmap/jquery.vmap.min.js"></script>

<script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>


<!-- =====================================================
     JQUERY KNOB
===================================================== -->

<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>


<!-- =====================================================
     MOMENT
===================================================== -->

<script src="../plugins/moment/moment.min.js"></script>


<!-- =====================================================
     DATERANGEPICKER
===================================================== -->

<script src="../plugins/daterangepicker/daterangepicker.js"></script>


<!-- =====================================================
     TEMPUSDOMINUS
===================================================== -->

<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>


<!-- =====================================================
     SUMMERNOTE
===================================================== -->

<script src="../plugins/summernote/summernote-bs4.min.js"></script>


<!-- =====================================================
     OVERLAY SCROLLBARS
===================================================== -->

<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>


<!-- =====================================================
     ADMINLTE
===================================================== -->

<script src="../dist/js/adminlte.js"></script>


<!-- =====================================================
     DASHBOARD
===================================================== -->

<script src="../dist/js/pages/dashboard.js"></script>


<!-- =====================================================
     ADMINLTE DEMO
===================================================== -->

<script src="../dist/js/demo.js"></script>


<!-- =====================================================
     DATATABLES
===================================================== -->

<script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>


<!-- =====================================================
     DATATABLES BUTTONS
===================================================== -->

<script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>


<!-- =====================================================
     JSZIP - EXCEL
===================================================== -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


<!-- =====================================================
     PDFMAKE
===================================================== -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- =====================================================
     DATATABLES HTML5 EXPORT
===================================================== -->

<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>


<!-- =====================================================
     DATATABLES PRINT
===================================================== -->

<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js"></script>


<!-- =====================================================
     DATATABLES - INICIALIZAÇÃO
===================================================== -->

<script>

$(document).ready(function () {

    /*
     * Verifica se a tabela #example existe
     * antes de inicializar o DataTables.
     */
    if ($('#example').length) {

        new DataTable('#example', {

            /*
             * BOTÕES
             */
            layout: {

                topStart: {

                    buttons: [
                        'copy',
                        'csv',
                        'excel',
                        'pdf',
                        'print'
                    ]

                }

            },


            /*
             * IDIOMA
             */
            language: {

                search: 'Pesquisar:',

                lengthMenu: 'Mostrar _MENU_ registros',

                info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',

                infoEmpty: 'Mostrando 0 até 0 de 0 registros',

                infoFiltered: '(filtrado de _MAX_ registros)',

                zeroRecords: 'Nenhum registro encontrado',

                emptyTable: 'Nenhum dado disponível na tabela',

                loadingRecords: 'Carregando...',

                processing: 'Processando...',

                paginate: {

                    first: 'Primeiro',

                    last: 'Último',

                    next: 'Próximo',

                    previous: 'Anterior'

                }

            }

        });

    }

});

</script>


</body>

</html>
