export default function shippingAddressForm() {

    let confValue = {
        from_id: 'shipping-address-form',
        formBlock: 'shipping-address-form-block',
        addressList: 'shipping-address-list'
    };

    let formBlock = $('#' + confValue.formBlock);
    if (formBlock.length > 0) {
        formBlock.append(generateForm());
    }

    let addressList = $('#' + confValue.addressList);
    if (addressList.length > 0) {
        getList();
    }

    function getList() {
        $.ajax({
            type: "GET",
            url: getApiUrlGet(),
            contentType: "application/json",
            dataType: "json",
            success: function (data) {
                handleListData(data);
            },
            error: function (result) {
                showErrorAlert($.parseJSON(result.responseText));
            }
        });
    }

    function handleListData(data) {
        $.each(data.collection, function (key, value) {
            addressList.append(getItemShippingAddress(value))
        });
    }

    function generateForm() {
        return $('<div />', {class: 'shipping-address-form-block'}).append(
            $('<form />', {action: '#', method: 'POST', id: confValue.from_id}).append(
                $('<div />', {class: 'form-group'}).append(
                    $('<label />', {for: 'address', text: 'Fill your shipping address'}),
                    $('<input />', {
                        id: 'address',
                        name: 'address',
                        placeholder: 'Beaker street',
                        type: 'text',
                        class: 'form-control',
                        'aria-describedby': "emailHelp"
                    }),
                    $('<small />', {id: 'emailHelp', class: 'form-text text-muted', text: 'your shipping address'})
                )
            ),
            getButton())
    }

    function getButton() {
        let button = $('<button />', {class: 'btn btn-primary', text: 'Submit'});

        button.on('click', function (e) {
            sendRequestAddressShipping();
        });

        return button;
    }

    function sendRequestAddressShipping() {
        $.ajax({
            type: "POST",
            url: getApiUrlPost(),
            data: prepareAjaxBody(),
            contentType: "application/json",
            dataType: "json",
            success: function (data) {
                clearInputsForm();
                renderSuccessfulData(data);
                showSuccessAlert();
            },
            error: function (result) {
                showErrorAlert($.parseJSON(result.responseText));

            }
        });
    }

    function prepareErrorMessage(obj) {
        let message = '';
        $.each(obj, function (key, value) {
            message += key + ": " + value + '<br>';
        });

        return $.parseHTML(message);
    }

    function showSuccessAlert() {
        $('.alert-success').show();
        setTimeout(function () {
            $('.alert-success').hide();
        }, 3000)
    }

    function showErrorAlert(result) {
        let textBody = document.getElementsByClassName('alert-danger')[0].getElementsByTagName('p')[0]
        $(textBody).text('').append(prepareErrorMessage(result));
        $('.alert-danger').show();
        setTimeout(function () {
            $('.alert-danger').hide();
        }, 3000)
    }

    function clearInputsForm() {
        $(':input', '#' + confValue.from_id)
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .prop('checked', false)
            .prop('selected', false);
    }

    function renderSuccessfulData(data) {
        return $('#shipping-address-list').prepend(getItemShippingAddress(data))
    }

    function getItemShippingAddress(data) {
        return $('<div />', {class: 'row'}).append(
            $('<div />', {class: 'col-sm border', text: data.address}),
            $('<div />', {class: 'col-sm border', text: data.serialized_created_at})
        );
    }

    function prepareAjaxBody() {
        return JSON.stringify(getFormObj(confValue.from_id));
    }

    function getApiUrlPost() {
        return window.Routing.generate(
            'post_shipping_address');
    }

    function getApiUrlGet() {
        return window.Routing.generate(
            'get_shipping_address_data');
    }

    function getFormObj(formId) {
        var formObj = {};
        var inputs = $('#' + formId).serializeArray();
        $.each(inputs, function (i, input) {
            formObj[input.name] = input.value;
        });
        return formObj;
    }

}
