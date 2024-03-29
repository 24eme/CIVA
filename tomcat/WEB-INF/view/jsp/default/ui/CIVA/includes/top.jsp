<%@ page session="true" %>
<%@ page pageEncoding="UTF-8" %>
<%@ page import="java.io.*" %>
<%@ page contentType="text/html; charset=UTF-8" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="spring" uri="http://www.springframework.org/tags" %>
<%@ taglib prefix="form" uri="http://www.springframework.org/tags/form" %>
<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<spring:theme code="mobile.custom.css.file" var="mobileCss" text="" />
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title>Espace des professionnels du Vignoble d’Alsace</title>
        <c:choose>
           <c:when test="${not empty requestScope['isMobile'] and not empty mobileCss}">
                <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
                <meta name="apple-mobile-web-app-capable" content="yes" />
                <meta name="apple-mobile-web-app-status-bar-style" content="black" />
                <link type="text/css" rel="stylesheet" media="screen" href="<c:url value="/css/fss-framework-1.1.2.css" />" />
                <link type="text/css" rel="stylesheet" href="<c:url value="/css/fss-mobile-${requestScope['browserType']}-layout.css" />" />
                <link type="text/css" rel="stylesheet" href="${mobileCss}" />
           </c:when>
           <c:otherwise>
                <link type="text/css" rel="stylesheet" href="<spring:theme code="standard.custom.css.file" />" />
                <script type="text/javascript" src="js/common_rosters.js"></script>
                <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
                <script type="text/javascript" src="js/sudoSlider.min.js"></script>
           </c:otherwise>
        </c:choose>
        <link rel="icon" href="<c:url value="/favicon.ico" />" type="image/x-icon" />
    </head>
    <body id="cas" onload="init();" class="fl-theme-iphone">
    <div class="flc-screenNavigator-view-container">
        <div class="fl-screenNavigator-view">
            <ul class="clearfix" id="liens_evitement"></ul>
            <div id="header" class="clearfix pngfix">
                <h1 id="logo"><a href="" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil"><img src="images/logo_civa.png" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" /></a></h1>

                <div id="titre_rubrique">
                    <h1>Espace des professionnels du Vignoble d’Alsace</h1>
                </div>
            </div>

            <div id="content" class="fl-screenNavigator-scroll-container">
