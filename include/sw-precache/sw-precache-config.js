module.exports = {
  staticFileGlobs: [
    'include/sw-precache/service-worker-registration.js',
    'jscalendar/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/Calendar4You/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/Mobile/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'kcfinder/{adapters,css,js,themes}/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/LD/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/ckeditor/{adapters,images,lang,plugins,skins,themes}/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/ckeditor/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/bunnyjs/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/dropzone/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/images/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/js/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/chart.js/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/jquery/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'include/csrfmagic/csrf-magic.js',
    'include/style.css',
    'include/print.css',
    'include/jquery.steps.css',
    'modules/com_vtiger_workflow/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/GlobalVariable/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/evvtMenu/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/MailManager/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/Tooltip/**/*.{js,css,png,jpg,gif,svg,eot,ttf,woff}',
    'modules/Accounts/Accounts.png',
    'modules/Assets/Assets.png',
    'modules/Calendar/Events.png',
    'modules/Campaigns/Campaigns.png',
    'modules/CobroPago/CobroPago.png',
    'modules/CobroPago/settings.png',
    'modules/Contacts/Contacts.png',
    'modules/Documents/Documents.png',
    'modules/Emails/Emails.png',
    'modules/Faq/Faq.png',
    'modules/HelpDesk/HelpDesk.png',
    'modules/Invoice/Invoice.png',
    'modules/Leads/Leads.png',
    'modules/MailManager/MailManager.png',
    'modules/ModComments/ModComments.png',
    'modules/PBXManager/PBXManager.png',
    'modules/Portal/Portal.png',
    'modules/Potentials/Potentials.png',
    'modules/PriceBooks/PriceBooks.png',
    'modules/Products/placeholder.gif',
    'modules/Products/Products.png',
    'modules/ProjectMilestone/ProjectMilestone.png',
    'modules/Project/Project.png',
    'modules/ProjectTask/ProjectTask.png',
    'modules/PurchaseOrder/PurchaseOrder.png',
    'modules/Quotes/Quotes.png',
    'modules/RecycleBin/RecycleBin.png',
    'modules/Reports/Reports.png',
    'modules/Rss/Rss.png',
    'modules/SalesOrder/SalesOrder.png',
    'modules/ServiceContracts/ServiceContracts.png',
    'modules/Services/placeholder.gif',
    'modules/Services/Services.png',
    'modules/Settings/Settings.png',
    'modules/SMSNotifier/SMSNotifier.png',
    'modules/Users/Users.png',
    'modules/Vendors/Vendors.png',
    'modules/Documents/Documents.js',
    'modules/SalesOrder/SalesOrder.js',
    'modules/Settings/Settings.js',
    'modules/Settings/profilePrivileges.js',
    'modules/Products/Productsslide.js',
    'modules/Products/multifile.js',
    'modules/Products/Products.js',
    'modules/ModComments/ModComments.js',
    'modules/ModComments/ModCommentsCommon.js',
    'modules/ProjectTask/ProjectTask.js',
    'modules/Project/Project.js',
    'modules/Assets/Assets.js',
    'modules/Emails/Emails.js',
    'modules/Rss/Rss.js',
    'modules/PickList/DependencyPicklist.js',
    'modules/HelpDesk/HelpDesk.js',
    'modules/Potentials/Potentials.js',
    'modules/CronTasks/CronTasks.js',
    'modules/PBXManager/PBXManager.js',
    'modules/Calendar/script.js',
    'modules/Calendar/Calendar.js',
    'modules/cbCalendar/cbCalendar.js',
    'modules/Portal/Portal.js',
    'modules/ProjectMilestone/ProjectMilestone.js',
    'modules/Leads/Leads.js',
    'modules/WSAPP/WSAPP.js',
    'modules/Accounts/Accounts.js',
    'modules/cbMap/cbMap.js',
    'modules/cbTermConditions/cbTermConditions.js',
    'modules/Campaigns/Campaigns.js',
    'modules/Invoice/Invoice.js',
    'modules/CobroPago/CobroPago.js',
    'modules/CustomerPortal/CustomerPortal.js',
    'modules/cbupdater/cbupdater.js',
    'modules/Home/Homestuff.js',
    'modules/Home/js/HelpMeNow.js',
    'modules/Faq/Faq.js',
    'modules/ModTracker/ModTracker.js',
    'modules/ModTracker/ModTrackerCommon.js',
    'modules/SMSNotifier/workflow/VTSMSTask.js',
    'modules/SMSNotifier/SMSNotifier.js',
    'modules/SMSNotifier/SMSConfigServer.js',
    'modules/SMSNotifier/SMSNotifierCommon.js',
    'modules/Contacts/Contacts.js',
    'modules/Dashboard/Dashboard.js',
    'modules/Services/Services.js',
    'modules/Vendors/Vendors.js',
    'modules/InventoryDetails/InventoryDetails.js',
    'modules/ServiceContracts/ServiceContracts.js',
    'modules/Users/Users.js',
    'modules/Import/resources/ImportStep2.js',
    'modules/Import/resources/Import.js',
    'modules/CustomView/CustomView.js',
    'modules/Reports/Reports.js',
    'modules/Reports/ReportsSteps.js',
    'modules/PriceBooks/PriceBooks.js',
    'modules/Quotes/Quotes.js',
    'modules/PurchaseOrder/PurchaseOrder.js',
    'include/calculator/calc.js',
    'include/Webservices/WSClient.js',
    'include/freetag/jquery.tagcanvas.js',
    'include/freetag/jquery.tagcanvas.min.js',
    'include/freetag/tagcanvas.min.js',
    'include/freetag/tagcanvas.js'
  ]
};
