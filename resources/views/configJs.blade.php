
<script>

    // this traslation for js external files

    window.translations = {
        charecterNotValid: 'test'
    };

    window.path = {
        mainURL : '{{url("/")}}',
        avatarDir: '{{asset("pictures/avatar/")}}',
        avatarTempDir: '{{asset("pictures/tempAvatar/")}}',
    };

    window.api = {
        token: '{{csrf_token()}}',
        tokenField: '{{csrf_field()}}'
    };

    window.url = {
        getFlightOfferCondition: '{{url("api/flight/getOfferCondition")}}'

    };

    window.dataTableLang = {

        processing: 'کمی صبر کنید ...',
        loadingRecords: 'در حال پردازش ...',
        search: 'جستجو : ',
        zeroRecords: 'رکوردی برای نمایش یافت نشد',
        lengthMenu: "نمایش _MENU_ رکورد",
        paginate: {
            first: "نخست" ,
            last: "آخرین",
            next: "بعدی",
            previous: "قبلی"
        },
        info: "نمایش _START_ تا _END_ از _TOTAL_ رکورد",
        infoEmpty: "نمایش 0 تا 0 از 0 رکورد",
        infoFiltered: "(از _MAX_ رکورد فیلتر شده است)",
        decimal: "",
        emptyTable: "داده ای در جدول وجود ندارد",
        thousands: ",",
        aria: {
            sortAscending: ": activate to sort column ascending",
            sortDescending: ": activate to sort column descending"
        }

    };

</script>
