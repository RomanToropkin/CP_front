var data = {};
var coord_scrol = 0;
var scrol_flag = 0;
var id = 0;

$(document).ready(function() {
    $.ajax({
        url: "http://projectcenter.ddns.net/api.php",
        cache: false,
        type: "GET",
        dataType: 'json',
        success: function(vars) {
            if (vars['auth']['is_moder'] != '1')
                location="/";
        },
        error: function() {
            alert("Error");
        }
    });
});

function registerSlids(selector, cursor) {
    $("#" + selector + " .js-range-slider").ionRangeSlider({
        min: 0,
        max: 100,
        from: cursor,
        skin: "round"
    });
}

$('body').on('change', '.js-range-slider', function(){
    coord_scrol = $(this).prop("value");
    id = $(this).attr('data-helper');
});

$('body').on('click', '.publish label', function(){
    if(!$(this).hasClass('active')) {
        id = $(this).attr('data-helper');
        if (id)
            request("module=moder&mode=update&container=is_show&value=1&id=" + id);
    }
});

$('body').on('click', '.stage label', function(){
    id = $(this).attr('data-helper');
    var value = $(this).children('input').val();
    request("module=moder&mode=update&container=id_stage&value="+ value +"&id=" + id);
});

$('body').mouseup(function(){
    if (scrol_flag) {
        scrol_flag = 0;
        request("module=moder&mode=update&container=progress&value=" + coord_scrol + "&id=" + id);
    }
});

$('body').on('mousedown', '.irs', function(){
    scrol_flag = 1;
});

$('body').on('blur', '.district input', function(){
    id = $(this).attr('data-helper');
    var value = $(this).val();
    request("module=moder&mode=update&container=district&value="+ value +"&id=" + id);
});

$('body').on('blur', '.type input', function(){
    id = $(this).attr('data-helper');
    var value = $(this).val();
    request("module=moder&mode=update&container=type&value="+ value +"&id=" + id);
});

$('.navbar-container button').click(function(){
    var val = $(this).val();
    if (val == 'no_hidden')
        request("module=moder&mode=get&is_show=1");
    else if (val == 'hidden')
        request("module=moder&mode=get&is_show=0");
    else if (val == 'logout')
        request("module=logout");
});

function request(params) {
    $.ajax({
        url: "http://projectcenter.ddns.net/api.php",
        cache: false,
        type: "GET",
        dataType: 'json',
        data: params,
        success: function(vars) {
            if (vars['logout'] == 'ok')
                location="/";
            else if (vars['result']) {
                data = vars['result'];
                generateContent();
            }
            console.log(vars);
        },
        error: function() {
            alert("Error");
        }
    });
}

function generateContent() {
    var content = "";
    for (var key in data) {
        content += ' \
            <a href="#" class="list-group-item list-group-item-action list-group-item-info main_list" data-target="#m'+ key +'" data-toggle="collapse"> \
                <span>'+ key +')</span> \
                <span>'+ data[key]['title'] +'</span> \
            </a> \
             \
            <div class="list-group pl-4 collapse curses" id="m'+ key +'"> \
                <a class="list-group-item list-group-item-action"> \
                   <h5 class="ml-5 m-0"> \
                       <span class="glyphicon glyphicon-align-center" aria-hidden="true"></span> \
                       Описание \
                   </h5> \
                    <span>'+ data[key]['dascription'] +'</span> \
                </a> \
                <a class="list-group-item list-group-item-action"> \
                   <div class="row"> \
                       <div class="col-2 type"> \
                           <h5 class="ml-5 m-0"> \
                               <span class="glyphicon glyphicon-tag" aria-hidden="true"></span> \
                               Тип \
                           </h5> \
                           <input type="text" class="form-control" placeholder="Тип" data-target="type" value="'+ data[key]['type'] +'" '+ ((data[key]['is_show'] == 1) ? "disabled" : "") +' data-helper="'+ data[key]['id_solution'] +'"> \
                       </div> \
                       <div class="col-2 district"> \
                           <h5 class="ml-5 m-0"> \
                               <span class="glyphicon glyphicon-record" aria-hidden="true"></span> \
                               Район \
                           </h5> \
                           <input type="text" class="form-control" placeholder="Район" data-target="district" value="'+ data[key]['district'] +'" '+ ((data[key]['is_show'] == 1) ? "disabled" : "") +' data-helper="'+ data[key]['id_solution'] +'"> \
                       </div> \
                       <div class="col stage"> \
                           <h5 class="ml-5 m-0"> \
                               <span class="glyphicon glyphicon-time" aria-hidden="true"></span> \
                               Стадия \
                           </h5> \
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons"> \
                              <label class="btn btn-outline-secondary '+ ((data[key]['id_stage'] == 0) ? "active" : "") +'" data-helper="'+ data[key]['id_solution'] +'"> \
                                <input type="radio" name="options" value="0" autocomplete="off" checked> Не выполнен \
                              </label> \
                              <label class="btn btn-outline-info '+ ((data[key]['id_stage'] == 1) ? "active" : "") +'" data-helper="'+ data[key]['id_solution'] +'"> \
                                <input type="radio" name="options" value="1" autocomplete="off"> В процессе \
                              </label> \
                              <label class="btn btn-outline-success '+ ((data[key]['id_stage'] == 2) ? "active" : "") +'" data-helper="'+ data[key]['id_solution'] +'"> \
                                <input type="radio" name="options" value="2" autocomplete="off"> Выполнен \
                              </label> \
                            </div> \
                       </div> \
                       <div class="col publish"> \
                           <h5 class="ml-5 m-0"> \
                               <span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span> \
                               Публикация \
                           </h5> \
                            <div class="btn-group btn-group-toggle w-100" data-toggle="'+ ((data[key]['is_show'] == 0) ? "buttons" : "") +'"> \
                              <label class="btn btn-outline-secondary '+ ((data[key]['is_show'] == 0) ? "active" : "disabled") +'"> \
                                <input type="radio" name="options" value="0" autocomplete="off" checked> Не опубликовано \
                              </label> \
                              <label class="btn btn-outline-success '+ ((data[key]['is_show'] == 1) ? "active" : "") +'" data-helper="'+ data[key]['id_solution'] +'"> \
                                <input type="radio" name="options" value="1" autocomplete="off"> Опубликовано \
                              </label> \
                            </div> \
                       </div> \
                   </div> \
                </a> \
                <a class="list-group-item list-group-item-action"> \
                   <h5 class="ml-5 m-0"> \
                       <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> \
                       Прогресс \
                   </h5> \
                    <div class="js-range-slider" data-helper="'+ data[key]['id_solution'] +'" data-toggle="progress"> \
                    </div> \
                </a> \
            </div>';
    }
    $('.main-content').html(content);
    for (var key in data)
        registerSlids("m"+key, data[key]['progress']);
}