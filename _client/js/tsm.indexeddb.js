/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
var idb_;
var idbRequest_;

function connectIDB(dbname,dbvers) {
    if ('webkitIndexedDB' in window) {
	window.indexedDB = window.webkitIndexedDB;
	window.IDBTransaction = window.webkitIDBTransaction;
	window.IDBKeyRange = window.webkitIDBKeyRange;
	console.info("indexedDB - webkit");
    } else if ('moz_indexedDB' in window) {
	window.indexedDB = window.moz_indexedDB;
	console.info("indexedDB - moz");
    }
    // Open our IndexedDB if the browser supports it.
    if (window.indexedDB) {
	idbRequest_ = window.indexedDB.open(dbname, dbvers);
	idbRequest_.onerror = idbError_;
	idbRequest_.addEventListener('success', function(event) {
	    idb_ = event.srcElement.result;
	    console.info("indexedDB --- OK");
	//var objNameStore="myObjectStoreTest";
	//idbShow_(objNameStore,event);
	}, false);
    }
}

function idbError_(event) {
    console.error('Error: '+event.message + ' (' + event.code + ')');
}

function createObjectStoreIDB (objNameStore) {
    if (!idb_) {
	if (idbRequest_) {
	//idbRequest_.addEventListener('success', removeObjectStore, false); // Если indexedDB еще открытия, просто очередь это.
	}
	console.info("return ERROR createObjectStoreIDB");
    } else {

	var request = idb_.setVersion('the new version string');
	request.onerror = idbError_;
	request.onsuccess = function(e) {
	    if (!idb_.objectStoreNames.contains(objNameStore)) {
		try {
		    var objectStore = idb_.createObjectStore(objNameStore, null); // FF is requiring the 2nd keyPath arg. It can be optional :(
		    console.info("Объект склад создан.");
		} catch (err) {
		    console.info("Error: "+err.toString());
		}
	    } else {
		console.info("Error => Объект склад уже существует.");
	    }
	}
    }
}

function addDataStoreIDB (objNameStore,idb_key,idb_value) {
    if (!idb_) {
	if (idbRequest_) {
	    idbRequest_.addEventListener('success', removeObjectStore, false); // If indexedDB is still opening, just queue this up.
	}
	console.info("return ERROR addDataStoreIDB");
    } else {

	if (!idb_.objectStoreNames.contains(objNameStore)) {				
	    console.info("Error => Объект склад не существует.");
	} else {
	    // Create a transaction that locks the world.
	    var objectStore = idb_.transaction([], IDBTransaction.READ_WRITE)
	    .objectStore(objNameStore);
	    var request = objectStore.put(
		idb_value,
		idb_key
		);
	}
    }
}

function showDataStoreIDB (objNameStore) {
    if (!idb_.objectStoreNames.contains(objNameStore)) {				
	console.info("Объект склад еще не созданы.");
    } else {

	var transaction = idb_.transaction([], IDBTransaction.READ_ONLY); // Read is default.
	var request = transaction.objectStore(objNameStore).openCursor(); // Get all results.

	// This callback will continue to be called until we have no more results.
	request.onsuccess = function(e) {
	    var cursor = e.srcElement.result;
	    if (!cursor) {
		console.info("'0' => '0'");
				    
	    } else {
		//console.info("'"+cursor.key+"' => '"+cursor.value+"'");
		//FIXME: NetBeans и сбощик Google Compiler JS выдают ошибку
		//cursor.continue();
	    }
	}
    }
}

function dropObjectStoreIDB (objNameStore) {
    if (!idb_) {
	if (idbRequest_) {
	    idbRequest_.addEventListener('success', removeObjectStore, false); // If indexedDB is still opening, just queue this up.
	}
	console.info("return ERROR dropObjectStore");
    }

    var request = idb_.setVersion("the new version string");
    request.onerror = idbError_;
    request.onsuccess = function(event) {

	if (idb_.objectStoreNames.contains(objNameStore)) {
	    try {
		// Spec has been updated to deleteObjectStore.
		if (idb_.deleteObjectStore) {
		    idb_.deleteObjectStore(objNameStore);
		} else {
		    idb_.removeObjectStore(objNameStore);
		}
		console.info("Объект склад удален.");
	    } catch (err) {
		console.info("Error: "+ err.toString());
	    }
	} else {
	    console.info("Error => Объект склад не существует.");
	}
    };
}          