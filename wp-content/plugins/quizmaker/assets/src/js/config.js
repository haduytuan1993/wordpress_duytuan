const baseUrl = quizmaker.site_url;
const config = {
  locale: 'en-US', // en-US, zh-CN
  url: baseUrl,
  ajaxUploadUrl: `${baseUrl}/admin/api/upload`,
  debug: {
    mock: false, // enable mock
    http: false // http request log
  },
  ajaxUrl: `${baseUrl}`
}

global.config = config

export default config