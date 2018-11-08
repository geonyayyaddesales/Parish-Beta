
Process recurring payment on cron

Get subscriptions where limit has not been reached (i.e. A subscription is setup for $20/month for 3 months)
	- If CIM
		- send each subscription to authorize 
		- add a row to transactions table


General architecture
	- Each time a transaction is added or updated, the "after payment" settings should be run.
	- user has list of payments
	- would be nice to allow payment on an existing entry
