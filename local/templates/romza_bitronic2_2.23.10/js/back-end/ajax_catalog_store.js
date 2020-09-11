function JCCatalogStoreSKU(arParams)
{
    if(!arParams) return;

    this.offers = arParams.OFFERS;
    this.CONTAINER_ID = arParams.CONTAINER_ID;
    BX.addCustomEvent(window, "onCatalogStoreProductChange", BX.proxy(this.offerOnChange, this));
}

JCCatalogStoreSKU.prototype.offerOnChange = function(id)
{
	if(typeof this.offers[id] == 'undefined') return;
	
	
	var storeAmountDiv = BX(this.CONTAINER_ID);
	if(storeAmountDiv)
	{
		if (0 === this.offers[id].DISPLAY_PROPERTIES.length)
		{
			BX.adjust(storeAmountDiv, {style: {display: 'none'}, html: ''});
		}
		else
		{
			BX.adjust(storeAmountDiv, {style: {display: ''}, html: this.offers[id].DISPLAY_PROPERTIES});
		}
	}
}