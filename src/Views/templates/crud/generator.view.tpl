<h1>Generador de CRUD</h1>

<p>Utiliza este generador para crear CRUDs de forma automática. Escribe el nombre de una tabla existente y genera el código necesario.</p>

<section class="grid">
    <div class="row">
        <strong>Tablas disponibles:</strong> {{tables}}
    </div>

    {{#if error}}
        <div style="color: red;">{{error}}</div>
    {{/if}}

    <form action="index.php?page=crud_generator" method="post">
        <label for="table">Nombre de la tabla:</label>
        <input type="text" name="table" id="table" placeholder="Escribe el nombre de la tabla" value="{{table}}" required>
        <button type="submit">Generar CRUD</button>
    </form>

    {{#if generated}}
        <h2>✅ CRUD generado para: {{table}}</h2>

        {{#each codeSections}}
            <article class="code-block">
                <h3>{{title}}</h3>
                <textarea readonly id="{{id}}" style="width: 100%; height: 250px; font-family: monospace;">{{{code}}}</textarea>
            </article>
        {{/each}}
    {{/if}}
</section>
