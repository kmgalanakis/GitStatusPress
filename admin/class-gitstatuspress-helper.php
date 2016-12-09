<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/kmgalanakis
 * @since      1.0.0
 *
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/admin
 * @author     Konstantinos Galanakis <kmgalanakis@gmail.com>
 */
class Gitstatuspress_Helper {

    public function file_time_string( $git_fetch_head_file_time ) {
        if( ! $git_fetch_head_file_time ) {
            return ' &#x292B;';
        }
        $time_elapsed = human_time_diff( $git_fetch_head_file_time );

        return sprintf( ' &#x2798; %s ago', $time_elapsed );
    }

    public function get_branch_info( $repository_path ) {

        $git_head_file = self::get_git_head_file_content( $repository_path );
        if ( ! $git_head_file ) {
            return '';
        }

        $branch_name = self::get_branch_name( $git_head_file );

        if ( ! $branch_name ) {
            return '';
        }

        $git_fetch_head_file_time = self::get_git_fetch_head_file_time( $repository_path );
        $last_pulled = self::file_time_string( $git_fetch_head_file_time );

        $result = sprintf( ' @ %s%s',  $branch_name, $last_pulled );

        return $result;

    }

    public function get_git_head_file_content( $repository_path ) {

        $head_file_path = self::construct_head_path( $repository_path );

        if ( is_file( $head_file_path ) && is_readable( $head_file_path ) ) {
            $file = file_get_contents( $head_file_path );
        }

        return isset( $file ) ? $file : null;

    }

    public function get_git_fetch_head_file_time( $repository_path ) {
        $head_fetch_file_path = self::construct_fetch_head_path( $repository_path );
        if (!is_file($head_fetch_file_path)) {
            $time = null;
        } else {
            $time = filemtime($head_fetch_file_path);
        }

        return  $time;
    }

    public function construct_fetch_head_path( $repository_path ) {
        $git_dir_name = self::git_directory_path( $repository_path );

        $file_path = $git_dir_name . DIRECTORY_SEPARATOR . 'FETCH_HEAD';

        return $file_path;
    }

    public function get_branch_name($file_content) {
        $lines = explode( "\n", $file_content );
        $branch_name = false;
        foreach ( $lines as $line ) {
            if ( strpos( $line, 'ref:' ) === 0 ) {
                $in_line = explode( "/", $line );

                // Handle special case with feature/issue-000 branch names
                if( 4 == count( $in_line ) && 'heads' == $in_line[1] ) {
                    $branch_name = $in_line[2] . '/' . $in_line[3];
                } else {
                    $branch_name = $in_line[ count( $in_line ) - 1 ];
                }
                break;
            }
        }

        return $branch_name;
    }

    public function construct_head_path( $repository_path ) {
        $git_dir_name = self::git_directory_path( $repository_path );

        $head_file_path = $git_dir_name . DIRECTORY_SEPARATOR . 'HEAD';

        return $head_file_path;
    }

    public function git_directory_path( $repository_path ) {
        $repository_path = untrailingslashit( $repository_path );

        if ( PHP_OS == "Windows" || PHP_OS == "WINNT" ) {
            $repository_path = str_replace( "/", DIRECTORY_SEPARATOR, $repository_path );
        }

        $git_dir_name = $repository_path . DIRECTORY_SEPARATOR . ".git";

        return $git_dir_name;
    }
}
