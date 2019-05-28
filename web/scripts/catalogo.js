'use strict'

$(document).ready(function()
{
    $("select").change(function()
    {
        $.ajax(
        {
            data: {
                parametros: $(this).children("option:selected").val()
            },
            url: "{{ (path('mostrar_catalogo') }}",
            type: "POST",
            success: function (data)
            {
                console.log(data)
            },
            error: function (data, xhr, status) 
            {
                alert("error "+status)
            }
        });
    });
});