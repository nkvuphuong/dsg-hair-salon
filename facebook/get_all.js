var elasticsearch = require('elasticsearch');
const esClient = new elasticsearch.Client({
    host: [
		{
			host: '188.166.233.118',
			auth: 'fff:#iEM@o2tzxn*5oUe',
			protocol: 'http',
			port: 9200
		}
	],
    log: 'error'
});
let body = {
	query : {
		bool : {
			should : [
				{ query_string: { query: '(fanpage_id: 1312866342157514) AND (NOT fb_content_sender_id: 1312866342157514)' } }
			]
		}
	},
	sort : [
		{ fb_content_timestamp : { order : "asc" } }
	],
	size : 10000,
};
esClient.search({ index : 'facebook', type : 'webhook', body : body }).then(results => {
	results.hits.hits.forEach(function(e) {
		console.log(e)
		// esClient.delete({
			// index : 'facebook',
			// type : 'webhook',
			// id : e._id
		// });
	});
	console.log(`${results.hits.total} items in ${results.took}ms`);
}).catch(console.error);