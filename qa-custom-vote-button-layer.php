<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	const TARGET_DATE = '2016-07-15';
	const AVATAR_SIZE = 80;

	public function head_css()
	{
		qa_html_theme_base::head_css();
		$plugin_url = qa_path('qa-plugin/q2a-custom-vote-button/');
		$css = $plugin_url . 'custom-vote-button.css';
		$this->output('<link rel="stylesheet" type="text/css" href="'.$css.'">');
	}

	public function main_part($key, $part)
	{
		if ($this->template === 'user' && $key === 'form_activity') {
			$newpart = $this->remove_downvote($part);
			qa_html_theme_base::main_part($key, $newpart);
			return;
		}
		qa_html_theme_base::main_part($key, $part);
	}

	public function voting_inner_html($post)
	{
		$this->vote_buttons($post);
		$this->vote_count($post);
		// モバイルでない かつ 投稿リスト内ではない
		if (!qa_is_mobile_probably() && !$this->is_q_list($post)) {
			$this->vote_avatars($post);
		}
		$this->vote_clear();
	}

	public function vote_buttons($post)
	{
		$this->output('<div class="qa-vote-buttons '.(($post['vote_view'] == 'updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

		switch (@$post['vote_state'])
		{
			case 'voted_up':
				$this->post_hover_button($post, 'vote_up_tags', 'thumb_up', 'mdl-button--colored qa-vote-one-button qa-voted-up');//mdl-button--colored クリックされたらボタンの色が変わる
				break;

			case 'voted_up_disabled':
				$this->post_disabled_button($post, 'vote_up_tags', 'thumb_up', 'qa-vote-one-button qa-vote-up');
				break;

			case 'voted_down':
				$this->post_hover_button($post, 'vote_down_tags', 'thumb_up', 'qa-vote-one-button qa-voted-down');
				break;

			case 'voted_down_disabled':
				$this->post_disabled_button($post, 'vote_down_tags', 'thumb_up', 'qa-vote-one-button qa-vote-down');
				break;

			case 'up_only':
				$this->post_hover_button($post, 'vote_up_tags', 'thumb_up', 'qa-vote-first-button qa-vote-up');
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;

			case 'enabled':
				$this->post_hover_button($post, 'vote_up_tags', 'thumb_up', 'qa-vote-first-button qa-vote-up');
				//$this->post_hover_button($post, 'vote_down_tags', 'thumb_up', 'qa-vote-second-button qa-vote-down');
				break;

			default:
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
		}

		$this->output('</div>');
	}

	public function post_hover_button($post, $element, $value, $class)
	{
		if (isset($post[$element]))
			// HTMLをinputからbuttonタグに変更し、アイコンを追加
			$this->output('<button '.$post[$element].' class="'.$class.'-button mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect"><i class="material-icons">'.$value.'</i></button>');
	}

	public function vote_count($post)
	{
		$post['netvotes_view']['prefix'] = '';
		$this->output('<span class="qa-vote-count '.(($post['vote_view'] == 'updown') ? 'qa-vote-count-updown' : 'qa-vote-count-net').'"'.@$post['vote_count_tags'].'>');

		if ($post['vote_view'] == 'updown') {
			$this->output_split($post['upvotes_view'], 'qa-upvote-count');
			$this->output_split($post['downvotes_view'], 'qa-downvote-count');

		}
		else
			$this->output_split($post['netvotes_view'], 'qa-netvote-count');

		$this->output('</span>');
	}

	public function body_footer()
	{
		qa_html_theme_base::body_footer();
		if (!qa_is_mobile_probably() && $this->template === 'question') {
			$plugin_url = qa_path('qa-plugin/q2a-custom-vote-button/');
			$script = $plugin_url . 'custom-vote-button.js';
			$this->output('<script type="text/javascript" src="'.$script.'"></script>');
		}
	}

	/**
	 * 支持した人のアイコンを表示する
	 * @param  array $post その投稿
	 * @return なし
	 */
	public function vote_avatars($post)
	{
		// 2016.10.18 現段階でアバターリスト表示させない

		//$voted_user_icons = $this->get_voted_user_icons($post);
		//$this->output('<div class="voted-avatar-list" >');
		//if (!empty($voted_user_icons)) {
		//	$this->output('<ul>');
		//	foreach ( $voted_user_icons as $icon ) {
		//		$this->output('<li class="qa-voted-avatar">'.$icon.'<li>');
		//	}
		//	$this->output('</ul>');
		//}
		//$this->output('<div style="clear:both;"></div>');
		//$this->output('</div>');
	}

	/**
	 * 支持しているユーザーのユーザーIDを収得
	 * @param  string $postid ポストID
	 * @return array         ユーザーID
	 */
	private function get_voted_users($postid)
	{
		if (empty($postid)) {
			return array();
		}

		$sql = "SELECT *
FROM ^users
WHERE userid
IN ( SELECT userid FROM ^uservotes WHERE postid = # AND vote = 1 )
";
		return qa_db_read_all_assoc(qa_db_query_sub($sql, $postid));
	}

	/**
	 * 支持したユーザーのアバターアイコンを取得
	 * @param  array $post その投稿
	 * @return array       アバターアイコンのhtml
	 */
	private function get_voted_user_icons($post)
	{
		$result = array();
		$postid = $post['raw']['postid'];
		$created = $post['raw']['created'];

		if (isset($postid) && $this->is_after_date($created, self::TARGET_DATE)) {
			$users = array();
			$users = $this->get_voted_users($postid);

			foreach ($users as $user) {
				if (QA_FINAL_EXTERNAL_USERS) {
					$result[]= qa_get_external_avatar_html($user['userid'], self::AVATAR_SIZE, false);
				} else {
					$result[]= qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'],
						$user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], self::AVATAR_SIZE);
				}
			}

		}
		return $result;
	}

	/**
	 * 投稿日指定日付より後かどうかを返す
	 * @param  string $created 投稿日のタイムスタンプ
	 * @param  string  $target  指定日付 YYYY-MM-DD
	 * @return boolean          指定日付以降ならtrue
	 */
	private function is_after_date($created, $target)
	{
		$targetDay = new DateTime($target);
		if ($created >= $targetDay->getTimestamp()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 質問リスト内の投稿かどうか
	 * @param  array  $post 現在の投稿
	 * @return boolean      投稿リスト内ならtrue
	 */
	private function is_q_list($post)
	{
		$keys = array_keys($post);
		if (count($keys) <= 19) {
			// ajax 呼び出しの場合keyの数が19個
			return false;
		}
		foreach ($keys as $key) {
			// 質問リストには c_list なし
			if ($key === 'c_list') {
				return false;
			}
		}
		return true;
	}

	private function remove_downvote($part)
	{
		$points = $this->content['raw']['points'];
		$upvotes = $points['aupvotes'] + $points['qupvotes'];
		$innervalue = '<span class="qa-uf-user-upvotes">'.number_format($upvotes).'</span>';
		$votegavevalue = (($upvotes == 1) ? qa_lang_html_sub('profile/1_up_vote', $innervalue, '1') : qa_lang_html_sub('profile/x_up_votes', $innervalue));
		$part['fields']['votegave']['value'] = $votegavevalue;

		$innervalue = '<span class="qa-uf-user-upvoteds">'.number_format($points['upvoteds']).'</span>';
		$votegotvalue = ((@$userpoints['upvoteds'] == 1) ? qa_lang_html_sub('profile/1_up_vote', $innervalue, '1')
			: qa_lang_html_sub('profile/x_up_votes', $innervalue));
		$part['fields']['votegot']['value'] = $votegotvalue;
		return $part;
	}
}
