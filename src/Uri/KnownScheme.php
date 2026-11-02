<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\Uri;

/**
 * ### Known URI schemes
 *
 * Defines commonly used URI schemes for identifying resources across network protocols, communication services, and
 * other resource identification systems.
 *
 * This enumeration is not exhaustive. Custom URI schemes are supported through the Scheme value object.
 * @since 1.0.0
 */
enum KnownScheme:string {

    /**
     * Hypertext Transfer Protocol.
     * @since 1.0.0
     */
    case HTTP = 'http';

    /**
     * Hypertext Transfer Protocol Secure.
     * @since 1.0.0
     */
    case HTTPS = 'https';

    /**
     * WebSocket Protocol.
     * @since 1.0.0
     */
    case WS = 'ws';

    /**
     * WebSocket Secure Protocol.
     * @since 1.0.0
     */
    case WSS = 'wss';

    /**
     * File Transfer Protocol.
     * @since 1.0.0
     */
    case FTP = 'ftp';

    /**
     * Secure Shell Protocol.
     * @since 1.0.0
     */
    case SSH = 'ssh';

    /**
     * Local file resources.
     * @since 1.0.0
     */
    case FILE = 'file';

    /**
     * Electronic mail addresses.
     * @since 1.0.0
     */
    case MAILTO = 'mailto';

    /**
     * Telephone numbers.
     * @since 1.0.0
     */
    case TEL = 'tel';

    /**
     * Short Message Service.
     * @since 1.0.0
     */
    case SMS = 'sms';

    /**
     * Uniform Resource Names.
     * @since 1.0.0
     */
    case URN = 'urn';

    /**
     * Inline data resources.
     * @since 1.0.0
     */
    case DATA = 'data';

    /**
     * Lightweight Directory Access Protocol.
     * @since 1.0.0
     */
    case LDAP = 'ldap';

    /**
     * Lightweight Directory Access Protocol over TLS.
     * @since 1.0.0
     */
    case LDAPS = 'ldaps';

    /**
     * Session Initiation Protocol.
     * @since 1.0.0
     */
    case SIP = 'sip';

    /**
     * Secure Session Initiation Protocol.
     * @since 1.0.0
     */
    case SIPS = 'sips';

    /**
     * Internet Relay Chat.
     * @since 1.0.0
     */
    case IRC = 'irc';

    /**
     * Secure Internet Relay Chat.
     * @since 1.0.0
     */
    case IRCS = 'ircs';

    /**
     * Network News Transfer Protocol.
     * @since 1.0.0
     */
    case NEWS = 'news';

    /**
     * Network News Transfer Protocol.
     * @since 1.0.0
     */
    case NNTP = 'nntp';

    /**
     * Git repository resources.
     * @since 1.0.0
     */
    case GIT = 'git';

}