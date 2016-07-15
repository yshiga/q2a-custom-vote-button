<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	public function head_css()
	{
		qa_html_theme_base::head_css();
		$plugin_url = '/qa-plugin/q2a-custom-vote-button/';
		$button_img = $plugin_url . 'img/thumbs.png';
		$css = "
.qa-vote-first-button {
	top: 12px;
}
.qa-vote-one-button {
	top: 12px;
}
.qa-netvote-count {
	margin-top: 17px;
}
.qa-vote-up-button,
.qa-vote-up-hover,
.qa-vote-up-disabled,
.qa-voted-up-button,
.qa-voted-up-hover {
	background: url($button_img) no-repeat;
	height: 30px;
	width: 30px;
}
.qa-vote-up-button {
    background-position: 0 0;
    color: #f1c96b;
}
.qa-vote-up-disabled {
    background-position: 0 -120px;
    color: #CCC;
}
.qa-vote-up-hover,
.qa-vote-up-button:hover {
    background-position: 0 -30px;
    color: #f1c96b;
}
.qa-voted-up-button {
    background-position: 0 -60px;
    color: #f1c96b;
}
.qa-voted-up-hover,
.qa-voted-up-button:hover {
    background-position: 0 -90px;
    color: #f1c96b;
}
";
		$this->output('<style>', $css, '</style>');
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

}
