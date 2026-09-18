<?php

use CodeIgniter\I18n\Time;

/**
 * Create Form Template based on Elements
 *
 * @param array $elements
 * @return string
 */
function render_elements_form(array $elements): string
{
    $xhtml = '';
    if (!empty($elements)) {
        foreach ($elements as $element) {
            $xhtml .= render_element_form($element);
        }
    }
    return $xhtml;
}

/**
 * Generate modal buttons
 *
 * @param string $btn1
 * @param string $btn2
 * @return string
 */
function modal_buttons(string $btn1 = 'Save', string $btn2 = 'Close'): string
{
    return sprintf(
        '<div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">%s</button>
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">%s</button>
        </div>',
        $btn1,
        $btn2
    );
}


/**
 * Generate modal buttons (alternative)
 *
 * @param string $btn1
 * @param string $btn2
 * @return string
 */
function modal_buttons2(string $btn1 = 'Save', string $btn2 = 'Close'): string
{
    return sprintf(
        '<div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">%s</button>
        </div>',
        $btn1
    );
}

/**
 * Get document status badge
 *
 * @param mixed $value
 * @return string
 */
function doc_status($value = ''): string
{
    switch ($value) {
        case 1:
            $c = 'bg-info';
            $t = 'Reviewing';
            break;
        case 2:
            $c = 'bg-warning';
            $t = 'Canceled';
            break;
        case 3:
            $c = 'bg-primary';
            $t = 'Verified';
            break;
        default:
            $c = 'bg-warning';
            $t = 'Sent for Reviewing';
            break;
    }

    return sprintf('<span class="badge light %s">%s</span>', $c, $t);
}

/**
 * Render a form element
 *
 * @param array $element
 * @param mixed $param
 * @return string
 */
function render_element_form(array $element, $param = null): string
{
    $xhtml = '';

    $type = $element['type'] ?? 'input';
    switch ($type) {
        case 'input':
        case 'password':
            $xhtml = sprintf(
                '<div class="%s">
                    <div class="form-group">
                        %s
                        %s
                    </div>
                </div> ',
                $element['class_main'],
                $element['label'],
                $element['element']
            );
            break;

        case 'switch':
            $xhtml = sprintf(
                '<div class="%s">
                    <label class="custom-switch">      
                        %s
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">%s</span> 
                    </label>
                </div> ',
                $element['class_main'],
                $element['element'],
                $element['label']
            );
            break;

        case 'checkbox':
            $xhtml = sprintf(
                '<div class="%s">
                    <div class="form-group">
                        <div class="custom-controls-stacked">
                            <label class="form-check">
                                %s
                                <span class="custom-control-label">&nbsp;%s</span>
                            </label>
                        </div>
                    </div>
                </div> ',
                $element['class_main'],
                $element['element'],
                $element['label']
            );
            break;

        case 'exchange_option':
            $item1_title = $element['item1']['name'];
            $item2_title = $element['item2']['name'];
            $xhtml = sprintf(
                '<div class="%s">
                    <div class="form-group">
                        %s
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text">1 %s =</span>
                            </span>
                            %s
                            <span class="input-group-append">
                                <span class="input-group-text new-currency-code"> %s </span>
                            </span>
                        </div>
                    </div>
                </div>',
                $element['class_main'],
                $element['label'],
                $item1_title,
                $element['element'],
                $item2_title
            );
            break;

        case 'admin-change-provider-service-list':
            $xhtml = sprintf(
                '<div class="%s">
                    <div class="dimmer">
                    <div class="loader"></div>
                    <div class="dimmer-content">
                        %s
                        %s
                    </div>
                    </div>
                </div>',
                $element['class_main'],
                $element['label'],
                $element['element']
            );
            break;

        case 'button':
            $xhtml = sprintf(
                '<div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        %s
                    </div>
                </div>',
                $element['element']
            );
            break;
    }

    return $xhtml;
}

/**
 * Create HTML for modal
 *
 * @param array $params
 * @return string
 */
function render_modal_html(array $params = []): string
{
    $params = [
        'btn-class'           => $params['btn-class'] ?? 'btn-outline-primary',
        'btn-title'           => $params['btn-title'] ?? 'Detail',
        'modal-id'            => $params['modal-id'] ?? 'modal-1',
        'modal-size'          => $params['modal-size'] ?? 'modal-lg',
        'modal-title'         => $params['modal-title'] ?? 'Modal Details',
        'modal-body-content'  => $params['modal-body-content'] ?? 'Modal content',
    ];

    $dataTarget = '#' . $params['modal-id'];

    return sprintf(
        '<button class="btn %s btn-sm" type="button" class="dash-btn" data-toggle="modal" data-target="%s">%s</button>
        <div id="%s" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog %s" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">%s</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="text-align:left">
                        %s
                    </div>
                </div>
            </div>
        </div>',
        $params['btn-class'],
        $dataTarget,
        $params['btn-title'],
        $params['modal-id'],
        $params['modal-size'],
        $params['modal-title'],
        $params['modal-body-content']
    );
}

/**
 * Render modal body content
 *
 * @param string $controllerName
 * @param array $item
 * @return string
 */
function render_modal_body_content(string $controllerName, array $item = []): string
{
    switch ($controllerName) {
        case 'refill':
            $apiName = $item['api_name'];
            $details = json_encode(json_decode($item['details']), JSON_PRETTY_PRINT);
            $date = convert_timezone($item['last_updated'], "user");
            return sprintf(
                '<div class="row justify-content-md-center">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label>%s</label>
                            <textarea rows="7" readonly class="form-control square">%s</textarea>
                        </div>
                    </div> 
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label>Last Update</label>
                            <input type="text" class="form-control square" readonly value="%s">
                        </div>
                    </div>
                </div>',
                $apiName,
                $details,
                $date
            );
            break;

        case 'services':
            $description = !empty($item['desc']) ? html_entity_decode($item['desc'], ENT_QUOTES) : '';
            $description = str_replace("\n", "<br>", $description);
            return sprintf(
                '<div class="form-group">
                    %s
                </div>',
                $description
            );
            break;
    }

    return '';
}

/**
 * Convert timezone
 *
 * @param string $dateTime
 * @param string $userTimeZone
 * @return string
 */
function convert_timezone(string $dateTime): string
{
    // Assuming $dateTime is in the 'UTC' timezone, adjust it if necessary
    $time = Time::parse($dateTime, 'UTC');

    // Retrieve userTimezone from the request
    $request = service('request');
    $userTimezone = $request->getJSON()->userTimezone ?? 'Asia/Dhaka';

    // Convert to the user's timezone
    $time->setTimezone($userTimezone);

    // Format the time as a string
    return $time->toLocalizedString();
}

if (!function_exists('show_device_status')) {
    function show_device_status($device_key = '', $uid = '')
    {
        $xhtml = null;
        $status = 'Expired';
        $type = 'warning';
        if (deviceValidation($device_key, $uid)) {
            $status = 'Active';
            $type = 'success';
        }
        $xhtml = sprintf('<small class="ml-auto badge bg-%s">%s</small>', $type, $status);
        return $xhtml;
    }
}

if (!function_exists('show_brand_status')) {
    function show_brand_status($brand_key = '', $uid = '')
    {
        $xhtml = null;
        $status = 'Expired';
        $type = 'warning';
        if (brandValidation($brand_key, $uid)) {
            $status = 'Active';
            $type = 'success';
        }
        $xhtml = sprintf('<small class="ml-auto badge bg-%s">%s</small>', $type, $status);
        return $xhtml;
    }
}
