$(document).ready(function() {
    
    const productTable = $('#productTable').DataTable({
        dom: "rt",
        language: {
            lengthMenu: '_MENU_',
            zeroRecords: '제품 타입을 추가해주세요.',
            info: '',
            infoEmpty: '',
            infoFiltered: ''
        },
        paging: false,
        ajax: {
            type: 'POST',
            url: '/page/ajax/a_code.php',
            data: {
                actionType: "get",
                code_group: "PD"
            }
        },
        select: {
            style: 'single'
        },
        columns: [{ data: 'code_name' }],
        order: [[0, "asc"]],
        columnDefs: []
    });

    // 제품 선택
    productTable.on( 'select', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            const $parent = $(this).closest(".grid-body");
            const data = productTable.rows(indexes).data()[0];
            $parent.find("input[name=idx]").val(data.idx);
            $parent.find("input[name=type]").val(data.code_name);
            $parent.find(".add-group").addClass("hidden");
            $parent.find(".modify-group").removeClass("hidden");
        }
    } );

    // 제품 선택 해제
    productTable.on( 'deselect', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            reset_form($(this));
        }
    } );

    const diseasesTable = $('#diseasesTable').DataTable({
        dom: "rt",
        language: {
            lengthMenu: '_MENU_',
            zeroRecords: '제품 타입을 추가해주세요.',
            info: '',
            infoEmpty: '',
            infoFiltered: ''
        },
        paging: false,
        ajax: {
            type: 'POST',
            url: '/page/ajax/a_code.php',
            data: {
                actionType: "get",
                code_group: "DI"
            }
        },
        select: {
            style: 'single'
        },
        columns: [{ data: 'code_name' }],
        order: [[0, "asc"]],
        columnDefs: []
    });

    // 질환 선택
    diseasesTable.on( 'select', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            const $parent = $(this).closest(".grid-body");
            const data = diseasesTable.rows(indexes).data()[0];
            $parent.find("input[name=idx]").val(data.idx);
            $parent.find("input[name=type]").val(data.code_name);
            $parent.find(".add-group").addClass("hidden");
            $parent.find(".modify-group").removeClass("hidden");
        }
    } );

    // 질환 선택 해제
    diseasesTable.on( 'deselect', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            reset_form($(this));
        }
    } );
    
    $(".btnAddType").on('click', function(e){
        const $parent = $(this).closest(".grid-body");
        var data = {
            'actionType' : 'insert',
            'idx': 0,
            'code_name': $parent.find("input[name=type]").val()
        };
        if($parent.hasClass("product")){
            data.code_group = 'PD';
            action(data, $(this), productTable);
        }else if($parent.hasClass("diseases")){
            data.code_group = 'DI';
            action(data, $(this), diseasesTable);
        }
    });

    $(".btnUpdateType").on('click', function(e){
        const $parent = $(this).closest(".grid-body");
        var data = {
            'actionType' : 'update',
            'idx': $parent.find("input[name=idx]").val(),
            'code_name': $parent.find("input[name=type]").val()
        };
        if($parent.hasClass("product")){
            data.code_group = 'PD';
            action(data, $(this), productTable);
        }else if($parent.hasClass("diseases")){
            data.code_group = 'DI';
            action(data, $(this), diseasesTable);
        }
    });

    $(".btnDeleteType").on('click', function(e){
        const $parent = $(this).closest(".grid-body");
        var data = {
            'actionType' : 'delete',
            'idx': $parent.find("input[name=idx]").val(),
        };
        
        if($parent.hasClass("product")){
            data.code_group = 'PD';
            action(data, $(this), productTable);
        }else if($parent.hasClass("diseases")){
            data.code_group = 'DI';
            action(data, $(this), diseasesTable);
        }
    });

    var reset_form = function(_$this){
        const $parent = _$this.closest(".grid-body");
        $parent.find("input[name=idx]").val(0);
        $parent.find("input[name=type]").val('');
        $parent.find(".add-group").removeClass("hidden");
        $parent.find(".modify-group").addClass("hidden");
    };

    var action = function(_data, _btn, _table){
        if (_data.actionType != 'delete') {
            if (!_data.code_name.length && _data.actionType != 'delete') {
                alert('타입을 입력해주세요.');
                return;
            }
        }
        
        _btn.prop('disabled', true);
        $.ajax({
            url: '/page/ajax/a_code.php',
            type: 'POST',
            data: _data,
            dataType:"json",
            success: function(res) {
                if(res.result == 'success'){
                    reset_form(_btn);
                    _table.ajax.reload();
                }else if(res.result == 'duplicate'){
                    alert("이미 추가되어있는 타입입니다.");
                    return;
                }else if(res.result == 'used'){
                    alert("사용 중인 타입입니다.");
                    return;
                }else{
                    alert("처리에 실패했습니다.");
                    return;
                }
                
            },
            complete: function(res) {
                _btn.prop('disabled', false);
            }

        });
        
    };
});