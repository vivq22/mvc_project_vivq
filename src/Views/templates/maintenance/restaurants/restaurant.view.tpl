<section class="depth-2 px-4 py-5">
    <h2>{{modeDsc}}</h2>
</section>

<section class="depth-2 px-4 py-4 my-4 grid row">
    <form method="POST" action="index.php?page=Examen-Maintenance-Restaurants-Restaurant&mode={{mode}}&id_restaurante={{id_restaurante}}"
        {{if readonly}} readonly disabled {{endif readonly}}
        {{if showConfirm}} class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3" {{else}} class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3" {{endif showConfirm}}>

        <div class="row my-2">
            <label for="id" class="col-12 col-m-4 col-l-3">Id:</label>
            <input type="text" name="id_restaurante" id="id_restaurante" value="{{id_restaurante}}" placeholder="Restaurant Id"
                class="col-12 col-m-8 col-l-9" readonly />
            <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

            <div class="row my-2">
                <label for="restaurant" class="col-12 col-m-4 col-l-3">Restaurant:</label>
                <input type="text" name="nombre" id="nombre" value="{{nombre}}" placeholder="Restaurant Name"
                    class="col-12 col-m-8 col-l-9" {{readonly}} />
                {{foreach errors_restaurant}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_restaurant}}
            </div>

            <div class="row my-2">
                <label for="cuisine_type" class="col-12 col-m-4 col-l-3">Cuisine Type:</label>
                <input type="text" name="tipo_cocina" id="tipo_cocina" value="{{tipo_cocina}}"
                    placeholder="Cuisine Type" class="col-12 col-m-8 col-l-9" {{readonly}} />
                {{foreach errors_tipo_cocina}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_tipo_cocina}}
            </div>

            <div class="row my-2">
                <label for="location" class="col-12 col-m-4 col-l-3">Location:</label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ubicacion}}" placeholder="Location"
                    class="col-12 col-m-8 col-l-9" {{readonly}} />
                {{foreach errors_ubicacion}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_ubicacion}}

                <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />

            </div>

            <div class="row my-2">
                <label for="rating" class="col-12 col-m-4 col-l-3">Rating:</label>
                <input type="number" name="calificacion" id="calificacion" value="{{calificacion}}"
                    placeholder="Rating (0-5)" class="col-12 col-m-8 col-l-9" {{readonly}} />
                {{foreach errors_calificacion}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_calificacion}}
            </div>

            <div class="row my-2">
                <label for="capacity" class="col-12 col-m-4 col-l-3">Capacity:</label>
                <input type="number" name="capacidad_comensales" id="capacidad_comensales"
                    value="{{capacidad_comensales}}" placeholder="Capacity of Diners" class="col-12 col-m-8 col-l-9"
                    {{readonly}} />
                {{foreach errors_capacidad_comensales}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_capacidad_comensales}}

                <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
            </div>

            <div class="row">
                <div class="col-12 right">
                    <button class="" id="btnCancel" type="button">{{cancelLabel}}</button>
                    &nbsp;
                    {{if showConfirm}}
                    <button class="primary" type="submit">Confirm</button>
                    {{endif showConfirm}}
                </div>
            </div>  

            {{if errors_global}}
            <div class="row">
                <div class="error col-12">{{errors_global}}</div>
            </div>
            {{endif errors_global}}

        </div>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", ()=>{
        document.getElementById("btnCancel")
            .addEventListener("click", (e)=>{
                e.preventDefault();
                e.stopPropagation();
                window.location.assign("index.php?page=Examen-Maintenance-Restaurants-Restaurants");
            });
    });
</script>