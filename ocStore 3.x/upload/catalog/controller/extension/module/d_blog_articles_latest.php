<?php
/**
 * Controller Module D.Blog Articles Latest
 *
 * @version 1.0
 * 
 * @author D.art <d.art.reply@gmail.com>
 */

class ControllerExtensionModuleDBlogArticlesLatest extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/d_blog_articles_latest');

        if ($this->request->server['HTTPS']) {
            $HTTP_SERVER = HTTPS_SERVER;
        } else {
            $HTTP_SERVER = HTTP_SERVER;
        }

        $this->document->addStyle($HTTP_SERVER . 'catalog/view/javascript/module-d_blog_articles_latest/d_blog_articles_latest.css');

        $this->load->model('blog/category');
        $this->load->model('blog/article');
        $this->load->model('tool/image');

        static $module = 0;

        $data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
        $data['description'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
        $data['attr_ID'] = $setting['attr_ID'];

        $category_info = $this->model_blog_category->getCategory($setting['blog_category_id']);

        $sort = 'p.date_added';
        $order = 'DESC';
        $page = 1;
        $limit = (int)$setting['quantity'];

        $data['text_all'] = sprintf($this->language->get('text_all'), $category_info['name']);
        $data['category_href'] = $this->url->link('blog/category', 'blog_category_id=' . $setting['blog_category_id']);

        $data['articles'] = array();

        $article_data = array(
            'filter_blog_category_id' => $setting['blog_category_id'],
            'sort'               => $sort,
            'order'              => $order,
            'start'              => 0,
            'limit'              => $limit
        );

        $results = $this->model_blog_article->getArticles($article_data);

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('configblog_image_article_width'), $this->config->get('configblog_image_article_height'));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('configblog_image_article_width'), $this->config->get('configblog_image_article_height'));
            }

            if ($this->config->get('configblog_review_status')) {
                $rating = (int)$result['rating'];
            } else {
                $rating = false;
            }

            $data['articles'][] = array(
                'article_id'  => $result['article_id'],
                'thumb'       => $image,
                'name'        => $result['name'],
                'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('configblog_article_description_length')) . '',
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'viewed'      => $result['viewed'],
                'rating'      => $result['rating'],
                'href'        => $this->url->link('blog/article', 'blog_category_id=' . $setting['blog_category_id'] . '&article_id=' . $result['article_id'])
            );
        }

        $data['module'] = $module++;

        return $this->load->view('extension/module/d_blog_articles_latest', $data);
    }
}