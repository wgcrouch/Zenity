<?php
/**
 * Simple class to allow php scripts to use Zenity to provide a limited GUI.
 * Zenity is a Gnome program so this will only work if gnome is installed.
 *
 * PHP version 5
 * 
 * @category Console
 * @package  Zenity
 * @author   Wayne Crouch <wgcrouch@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://github.com/wgcrouch/Zenity
 *
 */

/**
 * Zenity Class
 * 
 * @category Console
 * @package  Zenity
 * @author   Wayne Crouch <wgcrouch@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://github.com/wgcrouch/Zenity
 */
class Zenity
{

    protected $path;
    protected $handle;

    /**
     * Constructor
     *
     * @param string $path_to_zenity Path to the zenity executable
     */
    public function  __construct($path_to_zenity = '/usr/bin/zenity')
    {
        $this->path = $path_to_zenity;
    }

    /**
     * Show an error dialog, default icon is red and white (X)
     *
     * @param string $text   The error message to show
     * @param string $title  The text to show in the title bar
     * @param array  $params Any extra params you wish to set
     * 
     * @return void
     */
    public function showError($text, $title = 'Error', $params = array())
    {
        $this->executeBasic('error', $text, $title, false, $params);
    }

    /**
     * Show an info dialog, default icon is a lightbulb
     *
     * @param string $text   The  message to show
     * @param string $title  The text to show in the title bar
     * @param array  $params Any extra params you wish to set
     *
     * @return void
     */
    public function showInfo($text, $title = 'Info', $params = array())
    {
        $this->executeBasic('info', $text, $title, false, $params);
    }

     /**
     * Show an Display text information dialog.
     *
     * This allows the text of a file to be displayed, and edited.
     *
     * @param string  $filename The file to display
     * @param string  $title    The text to show in the title bar
     * @param boolean $editable If set to true the text will be editable and the
     *                          changed text will be returned. TODO: Fix the output
     * @param array   $params   Any extra params you wish to set
     *
     * @return void|string
     */
    public function showTextInfo($filename, $title = 'Text View', $editable = false, $params = array())
    {
        $params['filename'] = $filename;
        $return = false;
        if ($editable) {
            $return = true;
            $params['editable'] = null;
        }
        return $this->executeBasic('text-info', '', $title, $return, $params);
    }

    /**
     * Get a text value from the user
     *
     * @param string $text   The  message to show
     * @param string $title  The text to show in the title bar
     * @param array  $params Any extra params you wish to set
     *
     * @return void
     */
    public function getEntry($text, $title, $params = array())
    {
        return $this->executeBasic('entry', $text, $title,  true, $params);
    }

    /**
     * Show a calendar input to capture a date
     * 
     * @param string $text   Text to show
     * @param string $title  Title of window
     * @param array  $params Any extra params
     *
     * @return timestamp
     */
    public function getDate($text, $title, $params = array())
    {
        return $this->executeBasic('calendar', $text, $title,  true, $params);
    }

    /**
     * Show a file dialog to capture the path to a file
     * 
     * @param string  $title    Title of window
     * @param boolean $multiple If true allow multiple files to be selected
     * @param array   $params   Any extra params
     *
     * @return array
     */
    public function getFile($title, $multiple = false, $params = array())
    {
        if ($multiple) {
            $params['multiple'] = null;
        }
        $result =  $this->executeBasic('file-selection', null, $title, true, $params);


        $result = explode('|', $result);

        return $result;
    }


    const LIST_RADIO = 'radiolist';
    const LIST_CHECKLIST = 'checklist';

    public function showList($text = 'Select', $title = 'List', $data = array(), $type = null, $params = array())
    {
        $append = '';
        $columns = array_keys($data[0]);
        $values = '';
        foreach ($data as $row) {
            $values .= ' ';
            foreach ($row as $value) {
                $values .= escapeshellarg($value) . " ";
            }
            $values .= "\n";
        }

        foreach ($columns as $column) {
            $append .= ' --column=' . escapeshellarg(ucfirst($column)) . "\n";
        }

        $append .= $values;
        if ($type) {
            $params[$type] = null;
        }


        $result =  $this->executeBasic('list', $text, $title, true, $params, $append);

        return $result;
    }

    /**
     * Run zenity with a basic action that does not require any communucation with
     * the process
     * 
     * @param string  $action The action to tun
     * @param string  $text   The text to show in the dialog
     * @param string  $title  The title to show
     * @param boolean $return whether to return a value or not
     * @param array   $params Any extra params to send
     * @param string  $append Any extra stuff to append to the command
     * 
     * @return string
     */
    public function executeBasic($action, $text, $title, $return = false, $params = array(), $append = null)
    {
        $default_params = array(
            $action => null,
            'text'  => $text,
            'title' => $title,
        );

        $params = array_merge($params, $default_params);
        $cmd = $this->buildCommandFromParams($params, $append, $return);
        print $cmd;
        return exec($cmd);
    }

    /**
     * Show a progress par, should close with closeProgress
     * 
     * @param array $params Any extra params
     *
     * @return void
     */
    public function showProgress($params = array())
    {
        if (!empty($this->handle)) {
            $this->closeProgress();
        }
        $default_params = array(
            'progress' => null,
            'percentage' => 0,
            'auto-close' => null,
            'auto-kill' => null
        );

        $params = array_merge($params, $default_params);

        $cmd = $this->buildCommandFromParams($params);
        $this->handle = popen($cmd, 'w');
    }

    /**
     * Update the progress bar
     * 
     * @param int    $percentage The percentage complete from 1 to 100
     * @param string $text       The text to show above the bar, eg item 50/250
     *
     * @return void
     */
    public function updateProgress($percentage = 0, $text = 'Processing')
    {
        fwrite($this->handle, (int) $percentage . "\n");
        fwrite($this->handle, "#$text\n");
    }

    /**
     * Close the progress bar
     *
     * @return void
     */
    public function closeProgress()
    {
        pclose($this->handle);
    }

    /**
     * Build the command to execute.
     *
     * @param array   $params Array of params in the format param => value,
     *                        if no value, then set null
     * @param string  $append Any extra stuff to append to the command
     * @param boolean $return Whethe the command should return or not
     * 
     * @return string
     */
    protected function buildCommandFromParams($params, $append = null,  $return = false)
    {
        $cmd = $this->path;

        foreach ($params as $key => $val) {
            $cmd .= " --$key";
            if (!is_null($val)) {
                $cmd .= '=' . escapeshellarg($val);
            }
        }
        $cmd .= ' ' . $append;
        $cmd = escapeshellcmd($cmd);
        if ($return) {
            $cmd .= ' 2>&1';
        }
        return $cmd;
    }
}

?>
