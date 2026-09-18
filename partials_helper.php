<?php
defined('APP_NAMESPACE') OR exit('No direct script access allowed');
if (!function_exists('show_page_header_filter')) {
    function show_page_header_filter($controller_name, $data_filter = [],$type='')
    {
        $xhtml = null;
        $show_by_status_button = show_filter_status_button($controller_name, $data_filter['items_status_count'], $data_filter['params'],$type);
        $show_search_area      = show_search_area($controller_name, $data_filter['params']);
        $xhtml = sprintf(
            '<div class="mb-1">
                <div class="col-md-12">
                    <div class="card"> 
                        <div class="card-header">
                            <h3 class="card-title text-info"><i class="fa fa-filter" aria-hidden="true"></i></h3>
                            <div class="card-options">
                                <a href="#" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-controls="collapseExample"><i class="fas fa-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body collapse show" id="collapseExample">
                            <div class="row">
                                <div class="col-md-8">
                                 %s
                                </div>
                                <div class="col-md-4 search-area">
                                 %s
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>', $show_by_status_button, $show_search_area
        );
        return $xhtml;
    }
}

if (!function_exists('show_page_header')) {
    function show_page_header($controller_name, $params = [],$type='')
    {
        $xhtml = null;
        $xhtml_page_options = null;
        $class_page_type = (isset($params['page-options-type']) && $params['page-options-type'] == 'ajax-modal') ? 'ajaxModal' : '';
        switch ($params['page-options']) {
            case 'add-new':
                $add_new_link = ($type=='user')?user_url($controller_name . "/update"):admin_url($controller_name . "/update");
                $xhtml_page_options = sprintf(
                    '<div class="d-flex float-end">
                        <a href="%s" class="ml-auto btn btn-outline-primary %s">
                            <span class="fas fa-plus"></span>
                            Add new
                        </a>
                    </div>', $add_new_link, $class_page_type
                );
                break;
            case 'search':
                $show_search_area = show_search_area($controller_name, $params['search_params']);
                $xhtml_page_options = sprintf(
                    '<div class="search-area">
                        %s
                    </div>', $show_search_area
                );
                break;
        }

        $xhtml = sprintf(
            '<div class="row justify-content-between">
                <div class="col-6">
                    <h1 class="page-title">
                        <span class=""></span> %s
                    </h1>
                </div>
                <div class="col-6">
                    %s
                </div>
            </div>', ucfirst($controller_name), $xhtml_page_options
        );
        return $xhtml;
    }
}

