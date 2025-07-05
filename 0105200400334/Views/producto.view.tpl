<section class="depth-2 px-2 py-2">
    <h2>{{modeDsc}}</h2>
</section>

<section class="grid py-4 px-4 my-4">
    <div class="row">
        <div class="col-12 offset-m-1 col-m-10 offset-l-3 col-l-6">
            <form class="row" action="index.php?page=Examen-Producto&mode={{mode}}&id={{id_producto}}" method="post">
                <div class="row">
                    <label for="id_producto" class="col-12 col-m-4">Id</label>
                    <input readonly type="text" class="col-12 col-m-8" name="id_producto" id="id_producto"
                        value="{{id_producto}}" />
                    <input type="hidden" name="xsrToken" value="{{xsrToken}}" />
                </div>

                <div class="row">
                    <label for="nombre" class="col-12 col-m-4">Nombre</label>
                    <input type="text" class="col-12 col-m-8" name="nombre" id="nombre" value="{{nombre}}"
                        {{readonly}} />
                    {{if error_nombre}}
                    <span class="error col-12 col-m-8">{{error_nombre}}</span>
                    {{endif error_nombre}}
                </div>

                <div class="row">
                    <label for="tipo" class="col-12 col-m-4">Tipo</label>
                    <input type="text" class="col-12 col-m-8" name="tipo" id="tipo" value="{{tipo}}" {{readonly}} />
                    {{if error_tipo}}
                    <span class="error col-12 col-m-8">{{error_tipo}}</span>
                    {{endif error_tipo}}
                </div>

                <div class="row">
                    <label for="precio" class="col-12 col-m-4">Precio</label>
                    <input type="text" class="col-12 col-m-8" name="precio" id="precio" value="{{precio}}"
                        {{readonly}} />
                    {{if error_precio}}
                    <span class="error col-12 col-m-8">{{error_precio}}</span>
                    {{endif error_precio}}
                </div>

                <div class="row">
                    <label for="marca" class="col-12 col-m-4">Marca</label>
                    <input type="text" class="col-12 col-m-8" name="marca" id="marca" value="{{marca}}" {{readonly}} />
                    {{if error_marca}}
                    <span class="error col-12 col-m-8">{{error_marca}}</span>
                    {{endif error_marca}}
                </div>

                <div class="row">
                    <label for="fecha_lanzamiento" class="col-12 col-m-4">Fecha de Lanzamiento</label>
                    <input type="date" class="col-12 col-m-8" name="fecha_lanzamiento" id="fecha_lanzamiento"
                        value="{{fecha_lanzamiento}}" {{readonly}} />
                    {{if error_fecha_lanzamiento}}
                    <span class="error col-12 col-m-8">{{error_fecha_lanzamiento}}</span>
                    {{endif error_fecha_lanzamiento}}
                </div>

                <div class="row flex-end" style="margin-top: 20px;">
                    <button id="btnCancel">
                        {{if showAction}}
                        Cancelar
                        {{endif showAction}}
                        {{ifnot showAction}}
                        Volver
                        {{endifnot showAction}}
                    </button>
                    &nbsp;
                    {{if showAction}}
                    <button class="primary">Confirmar</button>
                    {{endif showAction}}
                </div>

                {{if error_global}}
                {{foreach error_global}}
                <div class="error col-12 col-m-8">{{this}}</div>
                {{endfor error_global}}
                {{endif error_global}}
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("btnCancel").addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            window.location.assign("index.php?page=Examen-Productos")
        });
    });
</script>