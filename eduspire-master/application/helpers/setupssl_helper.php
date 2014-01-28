<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*********************************************
@Helper Name					:		setupssl_helper
@Author							:		Janet Rajani
@Date							:		 Dec 30,2013
@Purpose						:		add functions for ssl
@Table referred					:		NA
@Table updated					:		NA
@Most Important Related Files	:		NA
********************************************/
# Chronological Development
#***********************************************************************************
#| Ref No.  |   Author name    | Date        | Severity     | Modification description
#***********************************************************************************

        /**************************************
        @Function Name 	 : is_https_on
        @Author        	 : Janet Rajani
        @Date          	 : Dec 30,2013
        @Purpose       	 : to check if https is setup or not
        @Parameters		 : NA
        ***************************************/
        #Chronological development
        #***********************************************************************************
        #| Ref No  | Name    | Date        | Purpose
        #*********************************************************************************** 
        function is_https_on()
        {
            if ( ! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' )
            {
                return FALSE;
            }

            return TRUE;
        }

	/**************************************
	@Function Name 	 : use_ssl
	@Author        	 : Janet Rajani
	@Date          	 : Dec 30,2013
	@Purpose       	 : change url in https mode
	@Parameters		 : $turn_on | boolean 
	***************************************/
	#Chronological development
	#***********************************************************************************
	#| Ref No  | Name    | Date        | Purpose
	#*********************************************************************************** 
	
        function use_ssl($turn_on = FALSE)
        {
            $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            if ( $turn_on )
            {
                if ( ! is_https_on() && $_SERVER['HTTP_HOST'] != 'localhost')
                {
                    redirect('https://' . $url, 'location', 301 );
                    exit;
                }
            }
            else
            {
                if ( is_https_on() )
                {
                    redirect('http://' . $url, 'location', 301 );
                    exit;
                }
            }
        }

/* End of file index.php */
/* Location: ./application/controllers/index.php */
