<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
        xmlns:html="http://www.w3.org/TR/REC-html40"
        xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="ar" dir="rtl">
        <head>
            <title>خريطة الموقع - نتيجتي</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style type="text/css">
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    color: #1e293b;
                    margin: 0;
                    padding: 0;
                    background: #f8fafc;
                    line-height: 1.5;
                }
                .header {
                    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
                    color: white;
                    padding: 40px 20px;
                    text-align: center;
                    margin-bottom: 30px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                }
                .header h1 {
                    margin: 0;
                    font-size: 2.5rem;
                    font-weight: 800;
                    letter-spacing: -0.025em;
                }
                .header p {
                    margin-top: 10px;
                    opacity: 0.9;
                    font-size: 1.1rem;
                }
                .container {
                    max-width: 1000px;
                    margin: 0 auto;
                    padding: 0 20px 40px;
                }
                .card {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    overflow: hidden;
                    border: 1px solid #e2e8f0;
                }
                .stats {
                    display: flex;
                    justify-content: space-between;
                    padding: 20px;
                    background: #f1f5f9;
                    border-bottom: 1px solid #e2e8f0;
                    font-weight: 600;
                    font-size: 0.875rem;
                    color: #475569;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }
                th {
                    background: #fff;
                    text-align: right;
                    padding: 16px 20px;
                    font-weight: 700;
                    font-size: 0.75rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    color: #64748b;
                    border-bottom: 2px solid #f1f5f9;
                }
                th.col-url { width: 65%; }
                th.col-date { width: 35%; }
                td {
                    padding: 14px 20px;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 0.875rem;
                    word-break: break-all;
                }
                tr:last-child td {
                    border-bottom: none;
                }
                tr:hover td {
                    background: #fdfdfd;
                }
                a {
                    color: #059669;
                    text-decoration: none;
                    transition: color 0.2s;
                    font-weight: 500;
                }
                a:hover {
                    color: #047857;
                    text-decoration: underline;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    color: #94a3b8;
                    font-size: 0.875rem;
                }
                .breadcrumb {
                    margin-bottom: 20px;
                    font-size: 0.875rem;
                }
                .breadcrumb a {
                    color: #64748b;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="container">
                    <h1>خريطة الموقع XML</h1>
                    <p>منصة نتيجتي - أسرع وصول لنتائج الطلاب في الوطن العربي</p>
                </div>
            </div>
            
            <div class="container">
                <div class="breadcrumb">
                    <a href="/">الرئيسية</a> / خريطة الموقع
                </div>

                <div class="card">
                    <div class="stats">
                        <span>إجمالي الروابط: <xsl:value-of select="count(sitemap:urlset/sitemap:url | sitemap:sitemapindex/sitemap:sitemap)"/></span>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th class="col-url">العنوان (URL)</th>
                                <th class="col-date">تاريخ التحديث</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                <tr>
                                    <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                                    <td><xsl:value-of select="sitemap:lastmod"/></td>
                                </tr>
                            </xsl:for-each>
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <tr>
                                    <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                                    <td><xsl:value-of select="sitemap:lastmod"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
                
                <div class="footer">
                    جميع الحقوق محفوظة © موقع نتيجتي 2025
                </div>
            </div>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
