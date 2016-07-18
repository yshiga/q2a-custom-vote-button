<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	const TARGET_DATE = '2016-07-15';
	const AVATAR_SIZE = 20;

	public function head_css()
	{
		qa_html_theme_base::head_css();
		$plugin_url = qa_path('qa-plugin/q2a-custom-vote-button/');
		$css = $plugin_url . 'custom-vote-button.css';
		$this->output('<link rel="stylesheet" type="text/css" href="'.$css.'">');
	}

	public function voting_inner_html($post)
	{
		$this->vote_buttons($post);
		$this->vote_count($post);
		if (!qa_is_mobile_probably()) {
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
				$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-one-button qa-voted-up');
				break;

			case 'voted_up_disabled':
				$this->post_disabled_button($post, 'vote_up_tags', '+', 'qa-vote-one-button qa-vote-up');
				break;

			case 'voted_down':
				// $this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-one-button qa-voted-down');
				break;

			case 'voted_down_disabled':
				// $this->post_disabled_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-one-button qa-vote-down');
				break;

			case 'up_only':
				$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-first-button qa-vote-up');
				// $this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;

			case 'enabled':
				$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-first-button qa-vote-up');
				// $this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-second-button qa-vote-down');
				break;

			default:
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				// $this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
		}

		$this->output('</div>');
	}

	public function vote_count($post)
	{
		$post['netvotes_view']['prefix'] = '';
		qa_html_theme_base::vote_count($post);
	}

	/**
	 * 支持した人のアイコンを表示する
	 * @param  array $post その投稿
	 * @return なし
	 */
	public function vote_avatars($post)
	{
		$voted_user_icons = $this->get_voted_user_icons($post);
		if (!empty($voted_user_icons)) {
			$this->output('<div class="voted-avatar-list" ><ul>');
			foreach ( $voted_user_icons as $icon ) {
				$this->output('<li class="qa-voted-avatar">'.$icon.'<li>');
			}
			$this->output('</ul></div>');
		}
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
}
