$(function () {

    function editableTable() {

        function restoreRow(oTable, nRow) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable.fnUpdate(aData[i], nRow, i, false);
            }
            oTable.fnDraw();
        }

        function editRow(oTable, nRow) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);
            jqTds[0].innerHTML = '<input type="text" class="form-control small" name="name_fa" value="' + aData[0] + '">';
            jqTds[1].innerHTML = '<input type="text" class="form-control small" name="name_en" value="' + aData[1] + '">';
            jqTds[2].innerHTML = '<input type="text" class="form-control small" name="name_ru" value="' + aData[2] + '">';
            jqTds[3].innerHTML = '<input type="text" class="form-control small" name="name_ar" value="' + aData[3] + '">';
            jqTds[4].innerHTML = '<div class="text-right"><a class="edit btn btn-sm btn-success" href="">ذخیره</a> <a class="delete btn btn-sm btn-danger" href=""><i class="icons-office-52"></i></a></div>';
        }

        function saveRow(oTable, nRow) {
            var jqInputs = $('input', nRow);
            oTable.fnUpdate(jqInputs[1].value, nRow, 0, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 1, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[4].value, nRow, 3, false);
            oTable.fnUpdate('<div class="text-right"><a class="edit btn btn-sm btn-default" href=""><i class="icon-note"></i></a> <a class="delete btn btn-sm btn-danger" href=""><i class="icons-office-52"></i></a></div>', nRow, 4, false);
            oTable.fnDraw();
        }

        function cancelEditRow(oTable, nRow) {
            var jqInputs = $('input', nRow);
            oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
            oTable.fnUpdate('<a class="edit btn btn-sm btn-default" href=""><i class="icon-note"></i></a>', nRow, 4, false);
            oTable.fnDraw();
        }

        function getSearchParametrs(elementID) {
            return $(`#${elementID}`).val();
        }
        // Constants
        const Path = path.mainURL + '/admin/';
        const enNameEl = $('#en_name_edit');
        const enFamilyEl = $('#en_family_edit');
        const nameEl = $('#name_edit');
        const familyEl = $('#family_edit');
        const maleEl = $('#male_edit');
        const femaleEl = $('#female_edit');
        const nationalCodeEl = $('#national_code_edit');
        const passportNumberEl = $('#passport_number_edit');
        const emailEl = $('#email_edit');
        const passwordEl = $('#password_edit');
        const ccMobileEl = $('#country-code-mobile-passenger-edit');
        const bcMobileEl = $('#before-code-mobile-passenger-edit');
        const mcMobileEl = $('#middleware-code-mobile-passenger-edit');
        const mbMobileEl = $('#mobile-number-passenger-edit');
        const avatarImageEl = $('#avatar-image-edit');
        const dataTableAjaxURL = Path + 'users/dataTables';
        const oTable = $('#table-editable').dataTable({
            "sLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            "Dom" : "<'row'<'col-md-6 filter-left'f><'col-md-6'T>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "oTableTools" : {
                "sSwfPath": "../assets/global/plugins/datatables/swf/copy_csv_xls_pdf.swf",
                "aButtons":[
                    {
                        "sExtends":"pdf",
                        "mColumns":[0, 1, 2, 3],
                        "sPdfOrientation":"landscape"
                    },
                    {
                        "sExtends":"print",
                        "mColumns":[0, 1, 2, 3],
                        "sPdfOrientation":"landscape"
                    },{
                        "sExtends":"xls",
                        "mColumns":[0, 1, 2, 3],
                        "sPdfOrientation":"landscape"
                    },{
                        "sExtends":"csv",
                        "mColumns":[0, 1, 2, 3],
                        "sPdfOrientation":"landscape"
                    }
                ]
            },
            // set the initial value
            "DisplayLength": 10,
            "processing": true,
            "serverSide": true,
            "order": [[0, 'desc']],
            "responsive": true,
            "searching": false,
            "Paginate": false,
            "PaginationType": "bootstrap",
            "oLanguage": dataTableLang,
            "ajax": {
                type: 'post',
                url: dataTableAjaxURL,
                data: function (d) {
                    d._token = api.token;
                    d.search_name = getSearchParametrs('search_name');
                    d.search_family = getSearchParametrs('search_family');
                    d.search_email = getSearchParametrs('search_email');
                    d.search_mobile = getSearchParametrs('search_mobile');
                    d.search_national = getSearchParametrs('search_national');
                    d.search_role = getSearchParametrs('search_role');
                }
            },
            "ColumnDefs": [{name: 'name'},
                {name: 'family'},
                {name: 'email'},
                {name: 'mobile'},
                {name: 'dob'},
                {name: 'national_code'},
                {name: 'roles'},
                {name: 'action'}
            ],
            "columns": [
                {data: 'name', sortable: false, searchable: true},
                {data: 'family', sortable: false, searchable: true},
                {data: 'email', sortable: false, searchable: true},
                {data: 'mobile', sortable: false, searchable: true},
                {data: 'dob', sortable: true, searchable: true},
                {data: 'national_code', sortable: false, searchable: true},
                {data: 'roles', sortable: false, searchable: true},
                {data: 'action', sortable: false, searchable: false}
            ],
            'initComplete': function () {
                $('#table-editable').show();
            }
        });

        jQuery('#table-edit_wrapper .dataTables_filter input').addClass("form-control medium"); // modify table search input
        jQuery('#table-edit_wrapper .dataTables_length select').addClass("form-control xsmall"); // modify table per page dropdown

        var nEditing = null;

        $('#table-edit_new').click(function (e) {
            e.preventDefault();
            $(".color-id").val(-1);
            var aiNew = oTable.fnAddData(['', '', '', '',
                    '<p class="text-center"><a class="edit btn btn-dark" href=""><i class="fa fa-pencil-square-o"></i>ویرایش</a> <a class="delete btn btn-danger" href=""><i class="fa fa-times-circle"></i> حذف</a></p>'
            ]);
            var nRow = oTable.fnGetNodes(aiNew[0]);
            editRow(oTable, nRow);
            nEditing = nRow;
        });

        $('#table-editable').on('click', 'a.delete', function (e) {
            e.preventDefault();
            var form = $(this).parents('form')[0];
            $(".color-id").val($($(this).parents('tr')[0]).data("id"));
            if (confirm("آیا از حذف این آیتم مطمئن هستید ؟") == false) {
                return;
            }
            var nRow = $(this).parents('tr')[0];
            $.ajax({
                url: "delete-color",
                type: "POST",
                data: $(form).serialize(),
                success: function(result){
                    if(result.status){
                        alert("با موفقیت حذف شد");
                        oTable.fnDeleteRow(nRow);
                    }else{
                        alert("مشکلی در سیستم بوجود آمده است. لطفا دوباره تلاش کنید");
                    }
                },
                error : function(xhr,status,error){
                    alert("مشکلی در سیستم بوجود آمده است. لطفا دوباره تلاش کنید");
                }
            });

            // alert("Deleted! Do not forget to do some ajax to sync with backend :)");
        });

        $('#table-editable').on('click', 'a.cancel', function (e) {
            e.preventDefault();
            if ($(this).attr("data-mode") == "new") {
                var nRow = $(this).parents('tr')[0];
                oTable.fnDeleteRow(nRow);
            } else {
                restoreRow(oTable, nEditing);
                nEditing = null;
            }
        });

        $('#table-editable').on('click', 'a.edit', function (e) {
            e.preventDefault();
            $(".color-id").val($($(this).parents('tr')[0]).data("id"));
            var form = $(this).parents('form')[0];
            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                restoreRow(oTable, nEditing);
                editRow(oTable, nRow);
                nEditing = nRow;
            } else if (nEditing == nRow && this.innerHTML == "ذخیره") {
                 /* This row is being edited and should be saved */
                $.ajax({
                    url: "update-color",
                    type: "POST",
                    data: $(form).serialize(),
                    success: function(result){
                        if(result.status){
                            alert("با موفقیت ذخیره شد");
                            saveRow(oTable, nEditing);
                        }else{
                            alert("مشکلی در سیستم بوجود آمده است. لطفا دوباره تلاش کنید");
                        }
                    },
                    error : function(xhr,status,error){
                        alert("مشکلی در سیستم بوجود آمده است. لطفا دوباره تلاش کنید");
                    }
                });

                nEditing = null;
                // alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                 /* No row currently being edited */
                editRow(oTable, nRow);
                nEditing = nRow;
            }
        });

        $('.dataTables_filter input').attr("placeholder", "جستجو ...");

    };

    editableTable();

});