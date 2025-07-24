<section class="container-m row px-4 py-4">
    <h1>{{FormTitle}}</h1>
</section>

<section class="container-m row px-4 py-4">
    {{with funcion}}
    <form action="index.php?page=Funciones_FuncionesForm&mode={{~mode}}&fncod={{fncod}}" method="POST"
        class="col-12 col-m-8 offset-m-2">

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="fncod">Código</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="fncod" id="fncod"
                placeholder="Código de la Función" value="{{fncod}}" />
            <input type="hidden" name="funcion_xss_token" value="{{~funcion_xss_token}}" {{if fncod_error}} <div
                class="col-12 col-m-9 offset-m-3 error">
            {{fncod_error}}
        </div>
        {{endif fncod_error}}
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="fndsc">Descripción</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="fndsc" id="fndsc"
                placeholder="Descripción de la Función" value="{{fndsc}}" />
            {{if fndsc_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{fndsc_error}}
            </div>
            {{endif fndsc_error}}
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="fnest">Estado</label>
            <select name="fnest" id="fnest" class="col-12 col-m-9" {{if ~readonly}} disabled {{endif ~readonly}}>
                <option value="ACT" {{fnest_act}}>Activo</option>
                <option value="INA" {{fnest_ina}}>Inactivo</option>
            </select>
            {{if fnest_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{fnest_error}}
            </div>
            {{endif fnest_error}}
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="fntyp">Tipo</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="fntyp" id="fntyp"
                placeholder="Tipo de la Función" value="{{fntyp}}" />
            {{if fntyp_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{fntyp_error}}
            </div>
            {{endif fntyp_error}}
        </div>

        {{endwith funcion}}

        <div class="row my-4 align-center flex-end">
            {{if showCommitBtn}}
            <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
            &nbsp;
            {{endif showCommitBtn}}
            <button class="col-12 col-m-2" type="button" id="btnCancelar">
                {{if showCommitBtn}}
                Cancelar
                {{endif showCommitBtn}}
                {{ifnot showCommitBtn}}
                Regresar
                {{endifnot showCommitBtn}}
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const btnCancelar = document.getElementById("btnCancelar");
        btnCancelar.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            window.location.assign("index.php?page=Funciones_FuncionesList");
        });
    });
</script>