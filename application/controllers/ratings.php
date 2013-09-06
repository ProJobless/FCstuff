<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RATINGS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Ratings extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->model('post');
        $this->load->model('rating');
        $this->load->model('notification');

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for rating posts.
     *
     * @access   public
     * @param    string
     */
    public function rate($ajax = FALSE)
    {
        log_access('ratings', 'rate');

        $post_id = $this->input->post('post_id');
        $rating  = $this->input->post('rating');

        if ($this->_rate($post_id, $rating))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('posts/' . $post_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Rate content.
     *
     * @access   private
     * @param    int       Post id
     * @param    int       Rating
     * @return   bool
     */
    private function _rate($post_id, $rating_score)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! ($post = $this->post->read($post_id)))
        {
            return FALSE;
        }

        if ( ! is_valid('rating', $rating_score))
        {
            return FALSE;
        }

        $rating = $this->rating->read($user['user_id'], $post_id);

        if ( ! isset($rating[0]))
        {
            $this->rating->create(array(
                'post_id' => $post_id,
                'user_id' => $user['user_id'],
                'rating'  => $rating_score
            ));

            $this->post->update($post_id, array(
                'rating_score' => $post[0]['rating_score'] + $rating_score,
                'rating_count' => $post[0]['rating_count'] + 1
            ));

            $this->user->update($user['user_id'], array(
                'reputation' => $user['reputation'] + $rating_score
            ));

            update_session_array();

            $this->notification->create(array(
                'user_id'  => $user['user_id'],
                'content'  => 'You got +' . $rating_score . ' reputation.',
                'link'     => 'people/me'
            ));

            if ( ! ($user['user_id'] == $post[0]['user_id']))
            {
                $op = $this->user->read($post[0]['user_id']);
                $this->user->update($post[0]['user_id'], array(
                    'reputation' => $op[0]['reputation'] + $rating_score
                ));

                $this->notification->create(array(
                    'user_id'  => $post[0]['user_id'],
                    'content'  => $user['name'] . ' gave your post a ' . $rating_score . ' star rating.',
                    'image'    => $user['user_id'] . '/' . $user['profile_picture'],
                    'link'     => 'posts/' . $post[0]['post_id']
                ));
            }

            $this->notification->create(array(
                'user_id'  => $post[0]['user_id'],
                'content'  => 'You got +' . $rating_score . ' reputation.',
                'image'    => '',
                'link'     => 'people/me'
            ));
        }

        else
        {
            $this->rating->update($rating[0]['rating_id'], array(
                'rating' => $rating_score
            ));

            $diff = $rating_score - $rating[0]['rating'];
            $new_rating = $post[0]['rating_score'] + $diff;

            $this->post->update($post_id, array(
                'rating_score' => $new_rating
            ));
        }

        return TRUE;
    }
}

/* End of file ratings.php */
/* File location : ./application/controllers/ratings.php */