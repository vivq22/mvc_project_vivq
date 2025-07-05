<section class="depth-2 px-2 py-2">
    <h2>Listado de Productos Electrónicos</h2>
</section>

<section class="WWList my-4">
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Marca</th>
                <th>Fecha de Lanzamiento</th>
                <th>
                    <a href="index.php?page=Examen-Producto&mode=INS&id=">
                        Nuevo
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach productos}}
            <tr>
                <td>{{id_producto}}</td>
                <td>{{nombre}}</td>
                <td>{{tipo}}</td>
                <td>{{precio}}</td>
                <td>{{marca}}</td>
                <td>{{fecha_lanzamiento}}</td>
                <td>
                    <a href="index.php?page=Examen-Producto&mode=DSP&id={{id_producto}}">
                        Ver
                    </a>
                    &nbsp;
                    <a href="index.php?page=Examen-Producto&mode=UPD&id={{id_producto}}">
                        Editar
                    </a>
                    &nbsp;
                    <a href="index.php?page=Examen-Producto&mode=DEL&id={{id_producto}}">
                        Eliminar
                    </a>
                </td>
            </tr>
            {{endfor productos}}
        </tbody>
    </table>
</section>