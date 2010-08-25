<%--

    Licensed to Jasig under one or more contributor license
    agreements. See the NOTICE file distributed with this work
    for additional information regarding copyright ownership.
    Jasig licenses this file to you under the Apache License,
    Version 2.0 (the "License"); you may not use this file
    except in compliance with the License. You may obtain a
    copy of the License at:

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing,
    software distributed under the License is distributed on
    an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
    KIND, either express or implied. See the License for the
    specific language governing permissions and limitations
    under the License.

--%>

<%@ page contentType="text/html; charset=UTF-8" %>
<jsp:directive.include file="includes/top.jsp" />

<form:form method="post" id="fm1" cssClass="fm-v clearfix" commandName="${commandName}" htmlEscape="true">

    <h2 class="titre_principal">Connexion</h2>
        
            <div class="box fl-panel" id="application_dr">

                <div id="nouvelle_declaration">
                    <div class="contenu_section">
                        <p class="intro"><spring:message code="screen.welcome.instructions" /> </p>

                        <form:errors path="*" cssClass="errors" id="status" element="div" />

                        <div class="ligne_form row fl-controls-left">
                            <label for="username" class="fl-label"><spring:message code="screen.welcome.label.netid" /></label>
                                                    <c:if test="${not empty sessionScope.openIdLocalId}">
                                                    <strong>${sessionScope.openIdLocalId}</strong>
                                                    <input type="hidden" id="username" name="username" value="${sessionScope.openIdLocalId}" />
                                                    </c:if>

                                                    <c:if test="${empty sessionScope.openIdLocalId}">
                                                    <spring:message code="screen.welcome.label.netid.accesskey" var="userNameAccessKey" />
                                                    <form:input cssClass="required" cssErrorClass="error" id="username" size="25" tabindex="1" accesskey="${userNameAccessKey}" path="username" autocomplete="false" htmlEscape="true" />
                                                    </c:if>
                        </div>


                        <div class="ligne_form row fl-controls-left">
                            <label for="password" class="fl-label"><spring:message code="screen.welcome.label.password" /></label>
                                                    <%--
                                                    NOTE: Certain browsers will offer the option of caching passwords for a user.  There is a non-standard attribute,
                                                    "autocomplete" that when set to "off" will tell certain browsers not to prompt to cache credentials.  For more
                                                    information, see the following web page:
                                                    http://www.geocities.com/technofundo/tech/web/ie_autocomplete.html
                                                    --%>
                                                    <spring:message code="screen.welcome.label.password.accesskey" var="passwordAccessKey" />
                                                    <form:password cssClass="required" cssErrorClass="error" id="password" size="25" tabindex="2" path="password"  accesskey="${passwordAccessKey}" htmlEscape="true" autocomplete="off" />
                        </div>

                        <div class="ligne_form row check">
                            <input id="warn" name="warn" value="true" tabindex="3" accesskey="<spring:message code="screen.welcome.label.warn.accesskey" />" type="checkbox" />
                            <label for="warn"><spring:message code="screen.welcome.label.warn" /></label>
                        </div>

                        <div class="ligne_form row btn-row ligne_btn">
                            <input type="hidden" name="lt" value="${flowExecutionKey}" />
                            <input type="hidden" name="_eventId" value="submit" />
                            <input class="btn" name="submit" accesskey="l" value="<spring:message code="screen.welcome.button.login" />" src="images/btn_valider.png" tabindex="4" type="image" />
                       </div>
                    </div>
                </div>
            </div>
    </form:form>
<jsp:directive.include file="includes/bottom.jsp" />
