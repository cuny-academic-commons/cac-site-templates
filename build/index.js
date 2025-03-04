/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/components/site-search.js":
/*!**********************************************!*\
  !*** ./assets/src/components/site-search.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SiteSearch: () => (/* binding */ SiteSearch)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
const {
  useState,
  useEffect,
  useRef
} = wp.element;

const SiteSearch = ({
  labelText,
  selectedSiteId,
  setSelectedSiteId
}) => {
  const [selectedSiteLabel, setSelectedSiteLabel] = useState('');
  const [options, setOptions] = useState([]); // ✅ Stores available dropdown options
  const siteLookup = useRef({}); // ✅ Stores ID → Label mapping

  // Fetch site by ID when mounting
  useEffect(() => {
    if (selectedSiteId > 0) {
      wp.apiFetch({
        path: `/cac-site-templates/v1/site/${selectedSiteId}`
      }).then(site => {
        const label = `${site.name} (${site.url})`;
        siteLookup.current[label] = site.id;

        // ✅ Update options first, THEN selected label
        setOptions([{
          label,
          value: label
        }]);
        setTimeout(() => setSelectedSiteLabel(label), 0); // ✅ Delay update slightly
      });
    }
  }, [selectedSiteId]);

  // Handle search input
  const searchSites = search => {
    if (!search) return;
    wp.apiFetch({
      path: wp.url.addQueryArgs(`/cac-site-templates/v1/site`, {
        search
      })
    }).then(sites => {
      siteLookup.current = {}; // ✅ Reset mapping to avoid stale entries
      const newOptions = sites.map(site => {
        const label = `${site.name} (${site.url})`;
        siteLookup.current[label] = site.id;
        return {
          label,
          value: label
        }; // ✅ Uses label as value for Combobox
      });
      setOptions(newOptions);
    });
  };

  // Handle selection change
  const onSelect = selectedLabel => {
    const selectedId = siteLookup.current[selectedLabel] || 0;
    setSelectedSiteId(selectedId);
    setSelectedSiteLabel(selectedLabel);
  };
  return /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.ComboboxControl, {
    label: labelText,
    value: selectedSiteLabel // ✅ Uses label for display
    ,

    onChange: onSelect // ✅ Maps label back to ID
    ,

    onFilterValueChange: searchSites // ✅ Dynamically filters results
    ,

    options: options
  });
};


/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["editPost"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["plugins"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************!*\
  !*** ./assets/src/index.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_site_search__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/site-search */ "./assets/src/components/site-search.js");






const CACSiteTemplateInfo = () => {
  const meta = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => select('core/editor').getEditedPostAttribute('meta') || {});
  console.log(meta);
  const {
    editPost
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)('core/editor');
  const updateMeta = (key, value) => {
    editPost({
      meta: {
        ...meta,
        [key]: value
      }
    });
  };
  return /*#__PURE__*/React.createElement(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__.PluginDocumentSettingPanel, {
    name: "cac-site-template-info",
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Site Template Info', 'cac-site-template-info')
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelRow, null, /*#__PURE__*/React.createElement(_components_site_search__WEBPACK_IMPORTED_MODULE_5__.SiteSearch, {
    labelText: "Template Site",
    setSelectedSites: selectedSites => updateMeta('selected-template-sites', selectedSites),
    setSelectedSiteId: siteId => updateMeta('template-site-id', siteId),
    selected: meta['selected-template-sites'] || [],
    selectedSiteId: meta['template-site-id'] || ''
  })), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelRow, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, {
    label: "Template Site Link Text",
    value: meta['template-site-link-text'] || '',
    onChange: newValue => updateMeta('template-site-link-text', newValue)
  })), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelRow, null, /*#__PURE__*/React.createElement(_components_site_search__WEBPACK_IMPORTED_MODULE_5__.SiteSearch, {
    labelText: "Demo Site",
    setSelectedSites: selectedSites => updateMeta('selected-demo-sites', selectedSites),
    setSelectedSiteId: siteId => updateMeta('demo-site-id', siteId),
    selected: meta['selected-demo-sites'] || [],
    selectedSiteId: meta['demo-site-id'] || ''
  })));
};
(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_0__.registerPlugin)('cac-site-template-info', {
  render: CACSiteTemplateInfo,
  icon: 'users'
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map