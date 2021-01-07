<div class="modal fullscreen-modal fade" id="addArticleList" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Artículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card border-primary">
                            <div class="card-header">
                            <button type="button" id="btnAddNewArticle" class="btn btn-success mb-3" onclick="addNewArticle();">Nuevo Articulo</button>
                                <table id="tableArticlesList" class="table table-sm table-hover" style="cursor:pointer">
                                    <thead>
                                        <tr>
                                            <th>Artículo</th>
                                            <th>Codigo</th>
                                            <th>Barras</th>
                                        </tr>
                                    </thead>
                                    <tbody>                    
                                    </tbody>
                                </table>
                                @include('buys.modals.newArticle')
                            </div>
                            <div class="card-body">
                                <table id="tableArticlesSelect" class="table-hover table-info table-responsive-lg compact" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="no-order fit">#Item</th>
                                            <th>Artículo</th>
                                            <th>Cantidad</th>
                                            <th>$ Neto</th>
                                            <th>% Desc.</th>
                                            <th>$ Desc.</th>
                                            <th>% IVA</th>
                                            <th>$ IVA</th>
                                            <th>$ Subtotal</th>
                                            <th class="no-order no-search fit"></th>
                                        </tr>
                                    </thead>
                                    <tbody>                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="footerAddArticle" class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="addListEnd();">Agregar</button>
            </div>
        </div>
    </div>
</div>