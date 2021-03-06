<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if( ! defined( 'NV_MAINFILE' ) )
{
    die( 'Stop!!!' );
}

if( ! nv_function_exists( 'nv_sale_popup' ) )
{
    /**
     * nv_block_config_relates_blocks()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_sale_popup( $module, $data_block, $lang_block )
    {
        global $nv_Cache, $db_config, $site_mods;

        $html = "<tr>";
        $html .= "	<td>" . $lang_block['blockid'] . "</td>";
        $html .= "	<td><select name=\"config_blockid\" class=\"form-control w200\">\n";
        $sql = "SELECT bid, " . NV_LANG_DATA . "_title," . NV_LANG_DATA . "_alias FROM " . $db_config['prefix'] . "_" . $site_mods[$module]['module_data'] . "_block_cat ORDER BY weight ASC";
        $list = $nv_Cache->db( $sql, 'catid', $module );
        foreach( $list as $l )
        {
            $sel = ( $data_block['blockid'] == $l['bid'] ) ? ' selected' : '';
            $html .= "<option value=\"" . $l['bid'] . "\" " . $sel . ">" . $l[NV_LANG_DATA . '_title'] . "</option>\n";
        }
        $html .= "	</select></td>\n";
        $html .= '<script type="text/javascript">';
        $html .= '	$("select[name=config_blockid]").change(function() {';
        $html .= '		$("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '	});';
        $html .= '</script>';
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['numrow'] . "</td>";
        $html .= "	<td><input class=\"form-control w100\" type=\"text\" name=\"config_numrow\" size=\"5\" value=\"" . $data_block['numrow'] . "\"/></td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['cut_num'] . "</td>";
        $html .= "	<td><input class=\"form-control w100\" type=\"text\" name=\"config_cut_num\" size=\"5\" value=\"" . $data_block['cut_num'] . "\"/></td>";
        $html .= "</tr>";

        return $html;
    }

    /**
     * nv_block_config_relates_blocks_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_sale_popup_submit( $module, $lang_block )
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int( 'config_blockid', 'post', 0 );
        $return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
        $return['config']['cut_num'] = $nv_Request->get_int( 'config_cut_num', 'post', 0 );
        return $return;
    }

    /**
     * nv_relates_product()
     *
     * @param mixed $block_config
     * @return
     */
    function nv_sale_popup( $block_config )
    {
        global $nv_Cache, $nv_Cache, $site_mods, $global_config, $lang_module, $module_config, $module_config, $module_name, $module_info, $global_array_shops_cat, $db_config, $my_head, $db, $pro_config, $money_config;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];

        if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $mod_file . '/block.sale_popup.tpl' ) )
        {
            $block_theme = $global_config['site_theme'];
        }
        else
        {
            $block_theme = 'default';
        }
        
        $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=';

        $xtpl = new XTemplate( 'block.sale_popup.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file );
        $xtpl->assign( 'LANG', $lang_module );

        if ($module != $module_name) {
            $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, numlinks, ' . NV_LANG_DATA . '_description AS description, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view, typeprice FROM ' . $db_config['prefix'] . '_' . $mod_data . '_catalogs ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            foreach ($list as $row) {
                $global_array_shops_cat[$row['catid']] = array(
                    'catid' => $row['catid'],
                    'parentid' => $row['parentid'],
                    'title' => $row['title'],
                    'alias' => $row['alias'],
                    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'],
                    'viewcat' => $row['viewcat'],
                    'numsubcat' => $row['numsubcat'],
                    'subcatid' => $row['subcatid'],
                    'numlinks' => $row['numlinks'],
                    'description' => $row['description'],
                    'inhome' => $row['inhome'],
                    'keywords' => $row['keywords'],
                    'groups_view' => $row['groups_view'],
                    'lev' => $row['lev'],
                    'typeprice' => $row['typeprice']
                );
            }
            unset($list, $row);

            // Css
            if (file_exists(NV_ROOTDIR . '/themes/' . $block_theme . '/css/' . $mod_file . '.css')) {
                $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/' . $mod_file . '.css" type="text/css" />';
            }

            // Language
            if (file_exists(NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php')) {
                require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php';
            }

            $pro_config = $module_config[$module];
        }

        $db->sqlreset()->select( 't1.id, t1.listcatid, t1.' . NV_LANG_DATA . '_title AS title, t1.' . NV_LANG_DATA . '_alias AS alias, t1.addtime, t1.homeimgfile, t1.homeimgthumb, t1.product_price, t1.money_unit, t1.discount_id, t1.showprice, t1.publtime, t1.product_number' )->from( $db_config['prefix'] . '_' . $mod_data . '_rows t1' )->join( 'INNER JOIN ' . $db_config['prefix'] . '_' . $mod_data . '_block t2 ON t1.id = t2.id' )->where( 't2.bid= ' . $block_config['blockid'] . ' AND t1.status =1' )->order( 't1.addtime DESC, t2.weight ASC' )->limit( $block_config['numrow'] );

        $list = $nv_Cache->db( $db->sql(), 'id', $module );

        $i = 0;
        $cut_num = $block_config['cut_num'];

        foreach( $list as $row )
        {
            if( $row['homeimgthumb'] == 1 )
            {
                //image thumb

                $src_img = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
            }
            elseif( $row['homeimgthumb'] == 2 )
            {
                //image file

                $src_img = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
            }
            elseif( $row['homeimgthumb'] == 3 )
            {
                //image url

                $src_img = $row['homeimgfile'];
            }
            else
            {
                //no image

                $src_img = NV_BASE_SITEURL . 'themes/' . $global_config['site_theme'] . '/images/shops/no-image.jpg';
            }

            $xtpl->assign('link', $link . $global_array_shops_cat[$row['listcatid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl']);

            $xtpl->assign( 'title', nv_clean60( $row['title'], $cut_num ) );
            $xtpl->assign( 'src_img', $src_img );
            $xtpl->assign( 'STT', $i );
            
            $xtpl->parse( 'main.loop' );
            ++$i;
        }

        $xtpl->parse( 'main' );
        return $xtpl->text( 'main' );
    }
}

if( defined( 'NV_SYSTEM' ) )
{
    $content = nv_sale_popup( $block_config );
}
