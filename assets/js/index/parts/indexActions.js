export default function shippingAddressForm() {

    let confValue = {
        from_id: 'shipping-address-form',
        formBlock: 'shipping-address-form-block',
        addressList: 'shipping-address-list',
        addressHeader: 'item_shipping_address_header',
        listAddresses: 'list_addresses'
    };

    let formBlock = $('#' + confValue.formBlock);
    if (formBlock.length > 0) {
        formBlock.append(generateForm());
    }

    let addressList = $('#' + confValue.addressList);
    if (addressList.length > 0) {
        getList();
    }
    addOnchangeToRadio();

    function addOnchangeToRadio() {
        $(document).on('change', '.select_default_address', function (e) {
            let current = $(this);
            updateAddressItem(current);
        });
    }

    function updateAddressItem(item) {
        $.ajax({
            type: "PUT",
            url: getApiUrlPut(),
            data: prepareAjaxItem(item),
            contentType: "application/json",
            dataType: "json",
            success: function (data) {
                showSuccessAlert();
            },
            error: function (result) {
                showErrorAlert($.parseJSON(result.responseText));
            }
        });
    }

    function getList() {
        $.ajax({
            type: "GET",
            url: getApiUrlGet() + '/?user_id=' + user_id,
            contentType: "application/json",
            dataType: "json",
            username: gate_access_user,
            password: gate_access_psw,
            success: function (data) {
                handleListData(data);
            },
            error: function (result) {
                showErrorAlert($.parseJSON(result.responseText));
            }
        });
    }

    function handleListData(data) {
        if (data.collection.length > 0) {
            setItemShippingAddressHeader();
            $.each(data.collection, function (key, value) {
                addressList.append(getItemShippingAddress(value))
            });
        }
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
        let textBody = document.getElementsByClassName('alert-danger')[0].getElementsByTagName('p')[0];
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
        if ($('.' + confValue.addressHeader).length < 1) {
            setItemShippingAddressHeader();
        }

        return $('.' + confValue.addressHeader).after(getItemShippingAddress(data))
    }

    function setItemShippingAddressHeader() {
        if ($('.' + confValue.addressHeader).length < 1
        ) {
            $('#' + confValue.addressList).append($('<div />', {class: 'row ' + confValue.addressHeader}).append(
                $('<div />', {class: 'col-sm border', text: 'Address'}),
                $('<div />', {class: 'col-sm border', text: 'Date'}),
                $('<div />', {class: 'col-sm border', text: 'Default'})
            ));
        }
    }

    function getItemShippingAddress(data) {
        return $('<div />', {class: 'row'}).append(
            $('<div />', {class: 'col-sm border', text: data.address}),
            $('<div />', {class: 'col-sm border', text: data.serialized_created_at}),
            $('<div />', {class: 'col-sm border'}).append(
                $('<input />', {
                    type: 'radio',
                    name: 'default',
                    value: data.id,
                    class: 'select_default_address',
                    checked: data.default_address
                })
            )
        );
    }

    function prepareAjaxItem(item) {
        return JSON.stringify({
            'id': item.val(),
            'default_address': true
        });
    }

    function prepareAjaxBody() {
        return JSON.stringify(getFormObj(confValue.from_id));
    }

    function getApiUrlPut() {
        return window.Routing.generate(
            'put_shipping_address');
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
