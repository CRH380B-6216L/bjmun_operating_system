# BJMUN Operating System (BJMUN OS)

An all-in-one platform for Beijing Model United Nations conferences

一个面向北京模联的一体化数字平台

This system was live as NUMPANEL from November 2016 till July 2017. It had served 3 conferences, 170+ registered schools, and 2,000+ users, with 500+ maximum DAU (daily active users), 235,000+ total visits, and 490,000+ CNY cash flow. For various reasons, however, BJMUN had canceled the contract with MUNPANEL, leaded MUNPANEL to stop its service and maintenance.

该系统曾于 2016 年 11 月至 2017 年 7 月之间，以 MUNPANEL 的名义服务了 3 场模拟联合国会议，共有 170 所注册学校、2000 名注册用户，最高日活跃用户 500 人以上，共计超过 23 万 5 千次访问，现金流达 490,000+ 人民币。然而由于种种原因，在 2017 年 8 月 BJMUN 与 MUNPANEL 解约，MUNPANEL 陷入停止运营与维护的状态。

However, in the context that the COVID-19 epidemic brings risks of conferences that cannot be held offline，in order to improve BJMUN's level of digitalization, and protect BJMUN's conference and user data safety in legitimization and privacy, BJMUN Association is about to reopen MUNPANEL system and transform it to a digital platform only for BJMUN - BJMUN Operating System, also abbreviated as BJMUN OS.

然而，在新型冠状病毒（2019 冠状病毒病）疫情导致会议存在不能线下举办风险的背景下，为了进一步提升北京模联会议的数字化水平、保证 BJMUN 的各类数据在国家安全和个人隐私方面的安全性，BJMUN 有意重启 MUNPANEL 项目，并将其转为 BJMUN 专属的数字化平台——BJMUN Operating System（下称 BJMUN OS）。

The development goal of BJMUN os is to make all the conference procedures and functionalities go online and first-party, in order that all-conference procedures will be able to conduct inside BJMUN OS without any third-party tools, which avoids any data stored in third-party systems and servers. When BJMUN holds offline conferences, the EasyChair moderator system, EasyFile file format system, EasyVote voting system, and other online-offline combining systems can still provide a convenient and efficient participation experience for conference attendees.

BJMUN OS 的开发目标为将所有会议流程与功能线上化且第一方化，最终实现所有会议流程均可在 BJMUN OS 内部完成，以避免各种数据存放于第三方系统和服务器中。在 BJMUN 举办线下会议时，BJMUN OS 提供的 EasyChair 主持、EasyFile 文件格式化、EasyVote 表决器等线上与线下结合的功能也可以为参会人员提供极大的便利。

## Specifications

BJMUN OS is neither same as [MUNPANEL](https://github.com/munpanel/MUNPANEL_v1) that connects all Model UN conferences, nor the [iPlacard](https://github.com/fengkaijia/iplacard), a digital system for previous IMUNC conferences, that uses singly per conference and discards all data afterward. To be continued...

BJMUN OS 既不同于过往的连结所有会议的 [MUNPANEL](https://github.com/munpanel/MUNPANEL_v1)，也不同于更早爱梦会议专用的一次性使用、会后清库的 [iPlacard](https://github.com/fengkaijia/iplacard)。未完待续……

## Plannings

First of all, we will discuss with BJMUN about the advantages and necessities of using first-party management system, instead of bundle of third-party applications, to manage conference and its data, plus recommond BJMUN Assosiation to adopt BJMUN OS as their _self-owned_ management system. Meanwhile, because that MUNPANEL (hereinafter referred to as "former system") was unmaintained for a long time，we have to check all codes, functions and how it works by deploy the former system into our personal computers (as all technical details of our system has been completely forgotten by 4 year passed).

首先，我们将向 BJMUN 组织团队宣讲使用第一方系统代替多个第三方应用组合管理会议数据与信息的优势和必要性，并建议 BJMUN 采纳 BJMUN OS 作为北京模联的会议管理系统使用。与此同时，由于 MUNPANEL 项目（下称“原系统”）常年缺乏维护，因此在重启开发前有必要先部署至本地计算机，查看原系统所有的功能并补习该系统的代码和运作方式（四年过去了，关于原系统的技术细节已经被忘得一干二净了）。

Then, we are planning to fully rewrite the UI to adopt the requirements from the BJMUN conferences. In addition to upgrade Laravel to latest version, we will also introduce Vue.js framework to front-end system in order to realize some real-timing functions that is difficult to be realized by PHP alone. We will also test and evaluate all functions inside former system and remove multiple-provider related parts from former system. 

之后，我们计划对 UI 进行彻底的改写。在这个过程中，我们除了升级 Laravel 版本之外，还计划向前端系统引入 Vue.js 框架，以便于实现单一 PHP 语言难以实现的实时性功能。另外，我们将对原系统内的各种功能系统进行测试与评估，并剔除原系统内对多主办者支持的相关功能。

Subsystems in further plannings, such as EasyChair moderator helper, EasyFile file formatting, EasyInterlock cross-venue joint management and EasyVote voting machine, will also be complemented, plus some functions didn't achieved by former system.

后续我们还将补全规划中的 EasyChair 主持、EasyFile 文件格式化、EasyInterlock 联动控制、EasyVote 表决器等子系统，并完成原系统尚未实现的部分功能。

## Maintainers

* Zirui Song: [GitHub](https://github.com/CRH380B-6216L)
* Anonymous developer

## License
[AGPLv3](LICENSE)
