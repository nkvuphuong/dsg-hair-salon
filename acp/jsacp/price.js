var Price = {
    containerItemObj: $("#priceItems"),
    containerProductsObj: $("#choose_product"),
    products: [], //to display on select
    allProducts: [], //to filter to this.products
    items: new Array(),
    setItems: function (items) {
        this.items = items;
        this.renderItems();

        //remove from products
        this.filterProductByItems();
        this.renderProducts();
    },
    setProducts: function (products) {
        this.allProducts = this.products = products;
        this.renderProducts();
    },
    renderItems: function () {
        this.containerItemObj.html("");
        if (this.items) {
            this.items.forEach(x => {
                this.containerItemObj.append(this.itemTpl(x));
            })
        }
    },
    renderProducts: function () {
        if (this.containerProductsObj.data('select2')) {
            this.containerProductsObj.select2('destroy');
        }

        this.containerProductsObj.html(`<option></option>`);

        let data = [];
        if (this.products) {
            this.products.forEach(x => {
                data.push({id: +x.product_id, text: x.product_name});
            })
        }

        this.containerProductsObj.select2({
            placeholder: 'Chọn SP/DV đưa vào bảng giá',
            width: '100%',
            data: data
        });
    },
    itemTpl: function (data) {
        let newPrice = typeof data.item_price !== 'undefined' ? +data.item_price : +data.product_price_sell;
        let oldPrice = typeof data.item_old_price !== 'undefined' ? +data.item_old_price : +data.product_price_sell;
        let html = `
      <tr>
        <td onclick="Price.removeItemByProductId('${data.product_id}')"><i class="fa fa-times" aria-hidden="true"></i></td>
        <td>${data.product_name}</td>
        <td>${data.product_price_sell}</td>
        <td><input class="form-control" type="number" id="item_old_price_${data.product_id}" name="items[${data.product_id}][old]" value="${oldPrice}" onchange="Price.setItemPriceByProductId('${data.product_id}', $(this).val(), 'old')"></td>
        <td><input class="form-control" type="number" id="item_news_price_${data.product_id}" name="items[${data.product_id}][new]" value="${newPrice}" onchange="Price.setItemPriceByProductId('${data.product_id}', $(this).val())"></td>
      </tr>
      `;
        return html;
    },
    filterProductByItems: function () {
        this.products = this.allProducts.filter(x => !this.items.find(y => x.product_id == y.product_id));
    },
    addItemByProductId: function (ProductId) {
        let item = this.products.filter(x => x.product_id == ProductId);
        if (item && item[0]) {
            this.items.unshift(item[0]); //Add to top of items
            this.renderItems();

            //remove from products
            this.filterProductByItems();
            this.renderProducts();
        }
    },
    removeItem: function (i) {
        this.items.splice(i, 1);
    },
    removeItemByProductId: function (productId) {
        productId = +productId;
        this.items = this.items.filter(x => x.product_id != productId);
        this.renderItems();

        this.filterProductByItems();
        this.renderProducts();
    },
    setItemPriceByProductId(productId, price, type = "new") {

        productId = +productId;
        price = +price;

        this.items.map(x => {
            if (x.product_id == productId) {
                if (type == 'old') {
                    x.item_old_price = price;
                } else {
                    x.item_price = price;
                }
            }
            return x;
        })
    },
    removeAllItems: function () {
        this.items = [];
        this.renderItems();

        this.filterProductByItems();
        this.renderProducts();
    }
}

var PriceStore = {
    mainContainer: $("#priceStoreDefault"),
    prices: [],
    stores: [],
    oriStores: [],
    setPrices: function (prices) {
        this.prices = prices;
    },
    setStores: function (stores) {
        this.oriStores = this.stores = stores;
    },
    render: function () {
        this.mainContainer.html("");
        if (this.stores.length) {
            this.stores.forEach(store => {
                this.mainContainer.append(this.tpl(store));
            })
        }
    },
    tpl: function (store) {
        let html = `
            <tr>
                <td class="three-dots">
                    ${store.store_name}
                </td>
                <td>
                    <span class="three-dots">${store.store_address}</span>
                </td>
                <td>
                    <select class="form-control" onchange="PriceStore.updatePriceId(${store.store_id}, $(this))">
                        <option value="0">Bảng giá chung</option>
                        ${this.renderPrices(store)}
                    </select>
                </td>
            </tr>
        `;
        return html;
    },
    renderPrices: function (store) {
        let html = "";

        let storeId = store.store_id;

        if (!storeId) return html;

        let prices = this.prices;

        prices = prices.filter(price => !price.price_stores || !price.price_stores.length || (price.price_stores.length > 0 && price.price_stores.indexOf(storeId) >= 0));

        if (prices && prices.length) {
            prices.forEach(price => {
                html += this.tplPrice(price, store.price_id);
            })
        }

        return html;
    },
    tplPrice: function (price, selected = 0) {
        let html = `<option value="${price.price_id}" ${+selected == +price.price_id ? 'selected' : ''}>${price.price_name}</option>`;
        return html;
    },
    updatePriceId: function (storeId, priceObj) {
        let _self = this;
        priceObj.prop("disabled", true);
        storeId = +storeId;
        let priceId = +priceObj.val();

        let url = `${site_root_domain}/?site=price&act=edit&subact=update_price_id&price_id=${priceId}&store_id=${storeId}`;

        $.ajax({
            url: url,
            success: function (res) {
                if (res.status == 'success') {
                    pNotifyACP(res.msg, 'success');
                    _self.oriStores.map(x => x.price_id = +x.store_id == storeId ? priceId : x.price_id);
                    _self.stores.map(x => x.price_id = +x.store_id == storeId ? priceId : x.price_id);
                } else {
                    pNotifyACP(res.msg, 'error');
                }
            },
            complete: function () {
                priceObj.prop("disabled", false);
            },
            error: function () {
                pNotifyACP('Error', 'error');
            }
        });
    },
    filterStores: function (keyword, type="name") {
        keyword = LibExt.convertVietnamese(keyword);
        if (keyword) {
            this.stores = this.oriStores.filter(store => {
                if(type == "address") {
                    let store_address = LibExt.convertVietnamese(store.store_address);
                    return store_address.includes(keyword)
                } else {
                    let store_name = LibExt.convertVietnamese(store.store_name);
                    return store_name.includes(keyword)
                }
            });
        } else {
            this.stores = this.oriStores;
        }

        this.render();
    }
}

var PriceCusGroup = {
    mainContainer: $("#priceCusGroupDefault"),
    prices: [],
    groups: [],
    oriGroups: [],
    setPrices: function (prices) {
        this.prices = prices;
    },
    setGroups: function (groups) {
        this.oriGroups = this.groups = groups;
    },
    render: function () {
        this.mainContainer.html("");
        if (this.groups.length) {
            this.groups.forEach(group => {
                this.mainContainer.append(this.tpl(group));
            })
        }
    },
    tpl: function (group) {
        let html = `
            <tr>
                <td class="three-dots">
                    ${group.gc_name}
                </td>
                <td>
                    <select class="form-control" onchange="PriceCusGroup.updatePriceId(${group.gc_id}, $(this))">
                        <option value="0">Bảng giá chung</option>
                        ${this.renderPrices(group)}
                    </select>
                </td>
            </tr>
        `;
        return html;
    },
    renderPrices: function (group) {
        let html = "";

        let groupId = group.gc_id;
        if (!groupId) return html;

        let prices = this.prices;

        prices = prices.filter(price => !price.price_cus_groups || !price.price_cus_groups.length || (price.price_cus_groups.length > 0 && price.price_cus_groups.indexOf(groupId) >= 0));

        if (prices && prices.length) {
            prices.forEach(price => {
                html += this.tplPrice(price, group.price_id);
            })
        }

        return html;
    },
    tplPrice: function (price, selected = 0) {
        let html = `<option value="${price.price_id}" ${+selected == +price.price_id ? 'selected' : ''}>${price.price_name}</option>`;
        return html;
    },
    updatePriceId: function (groupId, priceObj) {
        let _self = this;
        priceObj.prop("disabled", true);
        groupId = +groupId;
        let priceId = +priceObj.val();

        let url = `${site_root_domain}/?site=price&act=edit&subact=update_price_id&group_id=${groupId}&price_id=${priceId}`;

        $.ajax({
            url: url,
            success: function (res) {
                if (res.status == 'success') {
                    pNotifyACP(res.msg, 'success');
                    _self.oriGroups.map(x => x.price_id = +x.gc_id == groupId ? priceId : x.price_id);
                    _self.groups.map(x => x.price_id = +x.gc_id == groupId ? priceId : x.price_id);
                } else {
                    pNotifyACP(res.msg, 'error');
                }
            },
            complete: function () {
                priceObj.prop("disabled", false);
            },
            error: function () {
                pNotifyACP('Error', 'error');
            }
        });
    },
    filterGroups: function (keyword) {
        keyword = LibExt.convertVietnamese(keyword);
        if (keyword) {
            this.groups = this.oriGroups.filter(group => {
                let group_name = LibExt.convertVietnamese(group.gc_name);
                return group_name.includes(keyword)
            });
        } else {
            this.groups = this.oriGroups;
        }

        this.render();
    }
}