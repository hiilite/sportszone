<?php

$shortcodes = '';

$options = array(
    'event' => array(
        'details', 'results', 'performance'
    ),
    'team' => array(),
    'player' => array(
        'details', 'statistics'
    ),
);

$options = apply_filters( 'sportszone_shortcodes', $options );

foreach ( $options as $name => $group ) {
    if ( empty( $group ) ) continue;
    $shortcodes .= $name . '[' . implode( '|', $group ) . ']';
}

$raw = apply_filters( 'sportszone_tinymce_strings', array(
    'shortcodes' =>  $shortcodes,
    'insert' =>  __( 'SportsPress Shortcodes', 'sportszone' ),
    'auto' =>  __( 'Auto', 'sportszone' ),
    'manual' =>  __( 'Manual', 'sportszone' ),
    'select' =>  __( 'Select...', 'sportszone' ),
    'event' =>  __( 'Event', 'sportszone' ),
    'details' =>  __( 'Details', 'sportszone' ),
    'results' =>  __( 'Results', 'sportszone' ),
    'countdown' =>  __( 'Countdown', 'sportszone' ),
    'performance' =>  __( 'Box Score', 'sportszone' ),
    'calendar' =>  __( 'Calendar', 'sportszone' ),
    'statistics' =>  __( 'Statistics', 'sportszone' ),
    'team' =>  __( 'Team', 'sportszone' ),
    'standings' =>  __( 'League Table', 'sportszone' ),
    'player' =>  __( 'Player', 'sportszone' ),
    'list' =>  __( 'List', 'sportszone' ),
    'blocks' =>  __( 'Blocks', 'sportszone' ),
    'gallery' =>  __( 'Gallery', 'sportszone' ),
));

$formatted = array();

foreach ( $raw as $key => $value ) {
    $formatted[] = $key . ': "' . esc_js( $value ) . '"';
}

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
    sportszone:{
        ' . implode( ', ', $formatted ) . '
    }
}})';
