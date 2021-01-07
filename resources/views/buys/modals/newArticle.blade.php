<table id="addArticlesNew" class="table table-sm table-hover" style="display:none;">
    <thead>
        <tr>
            <th>Descripcion</th>
            <th>Lista de Precio</th>
        </tr>
    </thead>
    <tbody>  
        <tr>
            <td>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @include("commons.asterix-sm")<label>DESCRIPCION</label>
                    <input maxlength="60" type="text" id="new_article_description" class="form-control" placeholder="DESCRIPCION">
                </div>
            </td>
            <td>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @include("commons.asterix-sm")<label>LISTA DE PRECIO</label>
                    <input type="text" id="new_article_price_list" class="form-control" placeholder="PRECIO LISTA">
                </div>
            </td>
        </tr>                  
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <button type="button" class="btn btn-success float-right" onclick="storeNewArticle();">Guardar</button>
                <button type="button" class="btn btn-warning float-right mr-2" onclick="cancelNewArticle();">Cancelar</button>
            </td>
        </tr>
    </tfoot>
</table>
