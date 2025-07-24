<section class="container-m row px-4 py-4">
    <h1>{{pageTitle}}</h1>
</section>

<section class="container-m row px-4 py-4">
    <form method="post" action="index.php?page=Usuarios_UsuariosChangePassword&usercod={$usuario.usercod}" class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label for="userpswd" class="col-12 col-m-3">Nueva Contraseña:</label>
            <input type="password" id="userpswd" name="userpswd" class="col-12 col-m-9" required minlength="8"
                placeholder="Ingrese nueva contraseña">
        </div>

        <div class="row my-2 align-center">
            <label for="userpswd_confirm" class="col-12 col-m-3">Confirmar Contraseña:</label>
            <input type="password" id="userpswd_confirm" name="userpswd_confirm" class="col-12 col-m-9" required minlength="8"
                placeholder="Confirme la nueva contraseña">
        </div>

        <!-- Hidden inputs DENTRO del form -->
        <input type="hidden" name="usercod" value="{{usercod}}" />
        <input type="hidden" name="usuario_xss_token" value="{{usuario_xss_token}}" />


        <!-- Botones dentro de form -->
        <div class="row my-4 align-center flex-end">
            <div class="col-12 col-m-9 offset-m-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                &nbsp;
                <button class="btn btn-secondary" type="button" id="btnCancelar">Cancelar</button>
            </div>
        </div>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const btnCancelar = document.getElementById("btnCancelar");
        btnCancelar.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            window.location.assign("index.php?page=Usuarios_UsuariosList");
        });
    });
</script>