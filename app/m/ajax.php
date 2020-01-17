<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/

define('IN_AJAX', TRUE);


if (!defined('IN_ANWSION'))
{
	die;
}

define('IN_MOBILE', true);

class ajax extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'white';
		$rule_action['actions'] = array(
			'hot_topics_list',
            'search_hot_topics',
            'search_focus_topics',
            'hot_comment_list',
            'comment_list',
            'hot_answer_list',
            'answer_list',
            'get_index_data',
            'index_hot_list',
            'get_user_qu'
		);

		return $rule_action;
	}

	public function setup()
	{
		HTTP::no_cache_header();
	}

	public function favorite_list_action()
	{
		if ($_GET['tag'])
		{
			$this->crumb(AWS_APP::lang()->_t('标签') . ': ' . $_GET['tag'], '/favorite/tag-' . $_GET['tag']);
		}

		if ($action_list = $this->model('favorite')->get_item_list($_GET['tag'], $this->user_id, calc_page_limit($_GET['page'], get_setting('contents_per_page'))))
		{
			foreach ($action_list AS $key => $val)
			{
				$item_ids[] = $val['item_id'];
			}

			TPL::assign('list', $action_list);
		}
		else
		{
			if (!$_GET['page'] OR $_GET['page'] == 1)
			{
				$this->model('favorite')->remove_favorite_tag(null, null, $_GET['tag'], $this->user_id);
			}
		}

		TPL::output('m/ajax/favorite_list');
	}

	public function inbox_list_action()
	{
		if ($inbox_dialog = $this->model('message')->get_inbox_message($_GET['page'], get_setting('contents_per_page'), $this->user_id))
		{
			foreach ($inbox_dialog as $key => $val)
			{
				$dialog_ids[] = $val['id'];

				if ($this->user_id == $val['recipient_uid'])
				{
					$inbox_dialog_uids[] = $val['sender_uid'];
				}
				else
				{
					$inbox_dialog_uids[] = $val['recipient_uid'];
				}
			}
		}

		if ($inbox_dialog_uids)
		{
			if ($users_info_query = $this->model('account')->get_user_info_by_uids($inbox_dialog_uids))
			{
				foreach ($users_info_query as $user)
				{
					$users_info[$user['uid']] = $user;
				}
			}
		}

		if ($dialog_ids)
		{
			$last_message = $this->model('message')->get_last_messages($dialog_ids);
		}

		if ($inbox_dialog)
		{
			foreach ($inbox_dialog as $key => $value)
			{
				if ($value['recipient_uid'] == $this->user_id AND $value['recipient_count']) // 当前处于接收用户
				{
					$data[$key]['user_name'] = $users_info[$value['sender_uid']]['user_name'];
					$data[$key]['url_token'] = $users_info[$value['sender_uid']]['url_token'];

					$data[$key]['unread'] = $value['recipient_unread'];
					$data[$key]['count'] = $value['recipient_count'];

					$data[$key]['uid'] = $value['sender_uid'];
				}
				else if ($value['sender_uid'] == $this->user_id AND $value['sender_count']) // 当前处于发送用户
				{
					$data[$key]['user_name'] = $users_info[$value['recipient_uid']]['user_name'];
					$data[$key]['url_token'] = $users_info[$value['recipient_uid']]['url_token'];

					$data[$key]['unread'] = $value['sender_unread'];
					$data[$key]['count'] = $value['sender_count'];
					$data[$key]['uid'] = $value['recipient_uid'];
				}

				$data[$key]['last_message'] = $last_message[$value['id']];
				$data[$key]['update_time'] = $value['update_time'];
				$data[$key]['id'] = $value['id'];
			}
		}

		TPL::assign('list', $data);

		TPL::output('m/ajax/inbox_list');
	}

	public function hot_topics_list_action()
	{
		TPL::assign('hot_topics_list', $this->model('topic')->get_topic_list(null, 'discuss_count DESC', 5, $_GET['page']));

		TPL::output('m/ajax/hot_topics_list');
	}

    public function search_hot_topics_action()
    {
        $key = $_GET['keyword'];
        if ($key)
            $where = "topic_title like '%" . $key . "%' ";
        else
            $where = null;
        $data['info'] = $this->model('topic')->get_topic_list_by_js($where, 'discuss_count DESC', 10, intval($_GET['page']),0,$this->user_id);

        TPL::assign('hot_topics_list', $data['info']);
        $data['data'] = TPL::output('m/ajax/hot_topics_list',0);
        $data['pages'] = $this->model('topic')->found_rows()/10;
        echo json_encode($data);

    }

    public function search_focus_topics_action()
    {
        $key = $_GET['keyword'];
        $page = intval($_GET['page']);
        if ($key)
            $where = "topic_title like '%" . $key . "%' ";
        else
            $where = '1=1';
        $data['code']=1;
        $data = $this->model('topic')->get_focus_topic_by_js($this->user_id, '5', $where,$page);
       
        if($data)
        echo json_encode($data);
        else
        echo json_encode(array('code'=>0,'msg'=>'暂无内容'));
    }
    
    public function hot_comment_list_action()
    {
        $articleId = $_GET['articleId'];
        $page = $_GET['page'];
        if (!$article_info = $this->model('article')->get_article_info_by_id($articleId)) {
            TPL::output('m/ajax/a_answer');
        }
        $comments = $this->model('article')->get_comments($article_info['id'], $page, 10,"votes>0","votes DESC ,add_time DESC");
        if ($comments AND $this->user_id) {
            foreach ($comments AS $key => $val) {
                $comments[$key]['vote_info'] = $this->model('article')->get_article_vote_by_id('comment', $val['id'], 1, $this->user_id);
            }
        }
        TPL::assign('comments', $comments);
        TPL::assign("page",$page+1);
        TPL::assign("type","hot");
        TPL::output('m/ajax/a_answer');
    }
    public function comment_list_action()
    {
        $articleId = $_GET['articleId'];
        $page = $_GET['page'];
        if (!$article_info = $this->model('article')->get_article_info_by_id($articleId)) {
            TPL::output('m/ajax/a_answer');
        }
        $comments = $this->model('article')->get_comments($article_info['id'], $page, 10,"votes=0","add_time DESC");
        if ($comments AND $this->user_id) {
            foreach ($comments AS $key => $val) {
                $comments[$key]['vote_info'] = $this->model('article')->get_article_vote_by_id('comment', $val['id'], 1, $this->user_id);
            }
        }
        TPL::assign('comments', $comments);
        TPL::assign("page",$page+1);
        TPL::assign("type","normal");
        TPL::output('m/ajax/a_answer');
    }

    public function hot_answer_list_action()
    {
        $questionId = $_GET['questionId'];
        if (!$question_info = $this->model('question')->get_question_info_by_id($questionId)) {
            TPL::output('m/ajax/q_answer');
        }
        $where = "agree_count>0";
        if ($question_info['best_answer']) {
            $where .= " and answer_id !=" . $question_info['best_answer'];
        }
        $data = $this->model('answer')->get_answer_list_by_question_id($questionId, calc_page_limit($_GET['page'], 10), $where, "agree_count DESC,add_time DESC");
        $answer_ids = array();
        $answer_uids = array();
        foreach ($data as $answer) {
            $answer_ids[] = $answer['answer_id'];
            $answer_uids[] = $answer['uid'];

            if ($answer['has_attach']) {
                $has_attach_answer_ids[] = $answer['answer_id'];
            }
        }
        if ($answer_ids) {
            $answer_agree_users = $this->model('answer')->get_vote_user_by_answer_ids($answer_ids);

            $answer_vote_status = $this->model('answer')->get_answer_vote_status($answer_ids, $this->user_id);

            $answer_users_rated_thanks = $this->model('answer')->users_rated('thanks', $answer_ids, $this->user_id);
            $answer_users_rated_uninterested = $this->model('answer')->users_rated('uninterested', $answer_ids, $this->user_id);
            $answer_attachs = $this->model('publish')->get_attachs('answer', $has_attach_answer_ids, 'min');
        }
        foreach ($data as $key=>$answer) {
            if ($answer['has_attach']) {
                $data[$key]['attachs'] = $answer_attachs[$answer['answer_id']];

                foreach ($data[$key]['attachs'] as $k=>$val){
                    if(strpos($data[$key]['answer_content'],$val['file_location']) !== false){
                        $data[$key]['insert_attach_ids'][] = $val['id'];
                    }
                }

            }

            $data[$key]['user_rated_thanks'] = $answer_users_rated_thanks[$answer['answer_id']];
            $data[$key]['user_rated_uninterested'] = $answer_users_rated_uninterested[$answer['answer_id']];

            $data[$key]['answer_content'] = html_entity_decode($this->model('question')->parse_at_user(FORMAT::parse_attachs(FORMAT::parse_bbcode($answer['answer_content']))));
            $data[$key]['agree_users'] = $answer_agree_users[$answer['answer_id']];
            $data[$key]['agree_status'] = $answer_vote_status[$answer['answer_id']];
            $data[$key]['user_favorited'] = $this->model("favorite")->check_favorite($answer["answer_id"], "answer", $this->user_id);
        }

        TPL::assign('answers', $data);
        TPL::assign("page",$_GET['page']+1);
        TPL::assign("type","hot");
        TPL::output('m/ajax/q_answer');
    }

    public function answer_list_action()
    {
        $questionId = $_GET['questionId'];
        if (!$question_info = $this->model('question')->get_question_info_by_id($questionId)) {
            TPL::output('m/ajax/q_answer');
        }
        $where = "agree_count=0";
        if ($question_info['best_answer']) {
            $where .= " and answer_id!=" . $question_info['best_answer'];
        }
        $data = $this->model('answer')->get_answer_list_by_question_id($questionId, calc_page_limit($_GET['page'], 10), $where, "add_time DESC");
        $answer_ids = array();
        $answer_uids = array();
        foreach ($data as $answer) {
            $answer_ids[] = $answer['answer_id'];
            $answer_uids[] = $answer['uid'];

            if ($answer['has_attach']) {
                $has_attach_answer_ids[] = $answer['answer_id'];
            }
        }
        if ($answer_ids) {
            $answer_agree_users = $this->model('answer')->get_vote_user_by_answer_ids($answer_ids);

            $answer_vote_status = $this->model('answer')->get_answer_vote_status($answer_ids, $this->user_id);

            $answer_users_rated_thanks = $this->model('answer')->users_rated('thanks', $answer_ids, $this->user_id);
            $answer_users_rated_uninterested = $this->model('answer')->users_rated('uninterested', $answer_ids, $this->user_id);
            $answer_attachs = $this->model('publish')->get_attachs('answer', $has_attach_answer_ids, 'min');
        }

        foreach ($data as $key=>$answer) {
            if ($answer['has_attach']) {
                $data[$key]['attachs'] = $answer_attachs[$answer['answer_id']];
                foreach ($data[$key]['attachs'] as $k=>$val){
                    if(strpos($data[$key]['answer_content'],$val['file_location']) !== false){
                        $data[$key]['insert_attach_ids'][] = $val['id'];
                    }
                }
//                $data[$key]['insert_attach_ids'] = FORMAT::parse_attachs($answer['answer_content'], true);
            }

            $data[$key]['user_rated_thanks'] = $answer_users_rated_thanks[$answer['answer_id']];
            $data[$key]['user_rated_uninterested'] = $answer_users_rated_uninterested[$answer['answer_id']];

            $data[$key]['answer_content'] = html_entity_decode($this->model('question')->parse_at_user(FORMAT::parse_attachs(FORMAT::parse_bbcode($answer['answer_content']))));
            $data[$key]['agree_users'] = $answer_agree_users[$answer['answer_id']];
            $data[$key]['agree_status'] = $answer_vote_status[$answer['answer_id']];
            $data[$key]['user_favorited'] = $this->model("favorite")->check_favorite($answer["answer_id"], "answer", $this->user_id);
        }

        TPL::assign('answers', $data);
        TPL::assign("page",$_GET['page']+1);
        TPL::assign("type","normal");
        TPL::output('m/ajax/q_answer');
    }

    public function get_index_data_action($limit = 10)
    {
        $category_id = intval($_GET['category_id']);
        $type = $_GET['type'];
        $page = intval($_GET['page']);
        $is_ssl = $_GET['is_ssl'];
        //$data = $this->model('article')->get_mix_list($category_id,$is_ssl, $this->user_id, $page, $limit);
		$data = $this->model('article')->get_mix_list($category_id,$is_ssl, null, $page, $limit);
        TPL::assign('data', $data);
        TPL::assign('type', $type);
        TPL::assign('page', $page);
        TPL::assign('is_ssl', $is_ssl);
        TPL::output('m/ajax/get_index_data');
    }

    public function get_user_qu_action()
    {
        $offset = intval($_GET['page']) ? (intval($_GET['page'])-1) * 2 : 0;
        $page = intval($_GET['page']);
        $where = '';
        $users = $this->model('account')->get_users_by_counts(null, $where, 'fans_count desc,question_count desc,answer_count desc', 20, $offset);
        if (!$users) {
            $users = $this->model('account')->get_users_by_counts($this->user_id, $where, 'fans_count desc,question_count desc,answer_count desc', 2, 0);
            $page = 0;
        }
        TPL::assign('users', $users);
        TPL::assign('page', $page);

        TPL::output('m/ajax/get_user_qu');

    }

    public function index_hot_list_action()
    {
        $page = intval($_GET['page']) ? intval($_GET['page']) : 1;
        $categoryId = $_GET['categoryId'];
        if ($categoryId == 2) {
            $type = "article";
        }
        if($categoryId==3){
            $type = "question";
        }
        $hotList = $this->model('posts')->get_hot_posts($type, $categoryId, null, $_GET['day'], $page);
        TPL::assign('categoryId', $categoryId);
        TPL::assign('hotList', $hotList);
        TPL::output('m/ajax/index_hot_list');
    }
}