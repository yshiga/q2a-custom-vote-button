<?php

class qa_html_theme_layer extends qa_html_theme_base
{

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

	public function vote_avatars()
	{
		$this->output('<div class="voted-avatar-list" ><ul>');
		$avatar = '<li class="qa-voted-avatar">
	<a href="http://dev.q2a.com/user/testuser1" class="qa-avatar-link" original-title="" title=""><img src="http://dev.q2a.com/?qa=image&qa_blobid=7162451989466937721&qa_size=20" width="20" height="20" class="qa-avatar-image" alt=""></a>
</li>';
		for($i = 1; $i < 10; $i++) {
			$this->output($avatar);
		}
		$this->output('</ul></div>');
	}
}
