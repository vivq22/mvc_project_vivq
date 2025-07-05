<h1>Restaurants</h1>

<section class="WWList">
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Tipo Cocina</th>
                <th>Ubicación</th>
                <th>Calificación</th>
                <th>Capacidad Comensales</th>
                <th>
                    <a href="index.php?page=Maintenance-Restaurants-Restaurant&mode=INS&id=" class="">New</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach restaurants}}
            <tr>
                <td>{{id_restaurante}}</td>
                <td>{{nombre}}</td>
                <td>{{tipo_cocina}}</td>
                <td>{{ubicacion}}</td>
                <td>{{calificacion}}</td>
                <td>{{capacidad_comensales}}</td>
                <td>
                    <a href="index.php?page=Examen-Maintenance-Restaurants-Restaurant&mode=UPD&id_restaurante={{id_restaurante}}">
                        Edit
                    </a> &nbsp;
                    <a href="index.php?page=Examen-Maintenance-Restaurants-Restaurant&mode=DSP&id_restaurante={{id_restaurante}}">
                        View
                    </a> &nbsp;
                    <a href="index.php?page=Examen-Maintenance-Restaurants-Restaurant&mode=DEL&id_restaurante={{id_restaurante}}">
                        Delete
                    </a>
                </td>
            </tr>
            {{endfor restaurants}}
        </tbody>
    </table>

</section>