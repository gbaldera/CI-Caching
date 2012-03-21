<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI Caching
 *
 * Wrapper library for CodeIgniter's Cache driver
 * Based on the Cache Library from Phil Sturgeon (http://philsturgeon.co.uk/code/codeigniter-cache)
 *
 * @category	Libraries
 * @author		Gustavo Rod. Baldera
 * @link		https://github.com/gbaldera/ci-caching
 * @version		1.0
 */
class Caching
{
    private  $CI;
    protected $_adapter;
    protected $_backup;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->config('caching', TRUE);
        $this->_adapter = $this->CI->config->item('adapter', 'caching');
        $this->_backup = $this->CI->config->item('backup', 'caching');

        $this->CI->load->driver('cache', array('adapter' => $this->_adapter, 'backup' => $this->_backup));
    }

    /**
     * Get
     *
     * Look for a value in the cache.  If it exists, return the data
     * if not, return FALSE
     *
     * @param 	string
     * @return 	mixed		value that is stored/FALSE on failure
     */
    public function get($id)
    {
        return $this->CI->cache->get($id);
    }

    /**
     * Cache Save
     *
     * @param 	string		Unique Key
     * @param 	mixed		Data to store
     * @param 	int			Length of time (in seconds) to cache the data
     *
     * @return 	boolean		true on success/false on failure
     */
    public function save($id, $data, $ttl = 60)
    {
        return $this->CI->cache->save($id, $data, $ttl);
    }

    /**
     * Delete from Cache
     *
     * @param 	mixed		unique identifier of the item in the cache
     * @return 	boolean		true on success/false on failure
     */
    public function delete($id)
    {
        return $this->CI->cache->delete($id);
    }

    /**
     * Clean the cache
     *
     * @return 	boolean		false on failure/true on success
     */
    public function clean()
    {
        return $this->CI->cache->clean();
    }

    /**
     * Cache Info
     *
     * @param 	string		user/filehits
     * @return 	mixed		array on success, false on failure
     */
    public function cache_info($type = 'user')
    {
        return $this->CI->cache->cache_info($type);
    }

    /**
     * Get Cache Metadata
     *
     * @param 	mixed		key to get cache metadata on
     * @return 	mixed		return value from child method
     */
    public function get_metadata($id)
    {
        return $this->CI->cache->get_metadata($id);
    }

    /**
     * Is supported
     *
     * Is the actual driver supported in this environment?
     *
     * @return boolean
     */
    public function is_supported()
    {
        return $this->CI->cache->is_supported();
    }

    /**
     * Call a library's cached result or create new cache
     *
     * @access	public
     * @param	string
     * @return	array
     */
    public function library($library, $method, $arguments = array(), $expires = 60)
    {
        if ( ! class_exists(ucfirst($library)))
        {
            $this->CI->load->library($library);
        }

        return $this->_call($library, $method, $arguments, $expires);
    }

    /**
     * Call a model's cached result or create new cache
     *
     * @access	public
     * @return	array
     */
    public function model($model, $method, $arguments = array(), $expires = 60)
    {
        if ( ! class_exists(ucfirst($model)))
        {
            $this->CI->load->model($model);
        }

        return $this->_call($model, $method, $arguments, $expires);
    }

    private function _call($property, $method, $arguments = array(), $expires = 60)
    {
        $this->CI->load->helper('security');

        if ( !  is_array($arguments))
        {
            $arguments = (array) $arguments;
        }

        // Clean given arguments to a 0-index array
        $arguments = array_values($arguments);

        $cache_id = do_hash($property.$method.serialize($arguments), 'sha1') . $this->_adapter == 'file' ? '.cache' : '';

        // See if we have this cached or delete if $expires is negative
        if($expires >= 0)
        {
            $cached_response = $this->get($cache_id);
        }
        else
        {
            $this->delete($cache_id);
            return;
        }

        // Not FALSE? Return it
        if($cached_response !== FALSE && $cached_response !== NULL)
        {
            return $cached_response;
        }

        else
        {
            // Call the model or library with the method provided and the same arguments
            $new_response = call_user_func_array(array($this->CI->$property, $method), $arguments);
            $this->save($cache_id, $new_response, $expires);

            return $new_response;
        }
    }

}
