<?php
/**
 * Controller Module D.Blog Articles Latest
 *
 * @version 1.0
 * 
 * @author D.art <d.art.reply@gmail.com>
 */

class ControllerExtensionModuleDBlogArticlesLatest extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/d_blog_articles_latest');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('d_blog_articles_latest', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['category'])) {
            $data['error_category'] = $this->error['category'];
        } else {
            $data['error_category'] = '';
        }

        if (isset($this->error['quantity'])) {
            $data['error_quantity'] = $this->error['quantity'];
        } else {
            $data['error_quantity'] = '';
        }

        $url = '';

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/d_blog_articles_latest', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['action'] = $this->url->link('extension/module/d_blog_articles_latest', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->get['module_id'])) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['attr_ID'])) {
            $data['attr_ID'] = $this->request->post['attr_ID'];
        } elseif (!empty($module_info)) {
            $data['attr_ID'] = $module_info['attr_ID'];
        } else {
            $data['attr_ID'] = '';
        }

        // Categories
        $this->load->model('blog/category');

        $categories = $this->model_blog_category->getAllCategories();

        $data['categories'] = $this->model_blog_category->getCategories($categories);

        if (isset($this->request->post['blog_category_id'])) {
            $data['blog_category_id'] = $this->request->post['blog_category_id'];
        } elseif (!empty($module_info)) {
            $data['blog_category_id'] = $module_info['blog_category_id'];
        } else {
            $data['blog_category_id'] = 0;
        }

        if (isset($this->request->post['quantity'])) {
            $data['quantity'] = (int)$this->request->post['quantity'];
        } elseif (!empty($module_info)) {
            $data['quantity'] = (int)$module_info['quantity'];
        } else {
            $data['quantity'] = (int)$this->config->get('configblog_article_limit');
        }

        if (isset($this->request->post['module_description'])) {
            $data['module_description'] = $this->request->post['module_description'];
        } elseif (!empty($module_info)) {
            $data['module_description'] = $module_info['module_description'];
        } else {
            $data['module_description'] = array();
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        /* Languages */

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        /* Images */

        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/d_blog_articles_latest', $data));
    }

    /**
     * Validate Permission and Form.
     * 
     * @return bool
     */
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/d_blog_articles_latest')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ((int)$this->request->post['blog_category_id'] <= 0) {
            $this->error['category'] = $this->language->get('error_category');
        }

        if ((int)$this->request->post['quantity'] <= 0) {
            $this->error['quantity'] = $this->language->get('error_quantity');
        }

        return !$this->error;
    }
}